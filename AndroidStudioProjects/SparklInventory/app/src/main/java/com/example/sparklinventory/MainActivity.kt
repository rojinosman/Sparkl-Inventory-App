package com.example.sparklinventory

import android.os.Bundle
import android.util.Log
import android.view.KeyEvent
import android.widget.Toast
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.activity.enableEdgeToEdge
import androidx.compose.runtime.mutableIntStateOf
import androidx.compose.runtime.mutableStateListOf
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import com.example.sparklinventory.data.SparklApiClient
import com.example.sparklinventory.data.TokenStore
import com.example.sparklinventory.ui.SparklMainScreen
import com.example.sparklinventory.ui.theme.SparklInventoryTheme
import com.rscja.team.qcom.deviceapi.B
import com.rscja.team.qcom.deviceapi.G
import com.rscja.deviceapi.entity.UHFTAGInfo
import okhttp3.*
import java.io.IOException
import java.util.concurrent.ConcurrentHashMap

class MainActivity : ComponentActivity() {

    private var rfid: G? = null
    @Volatile private var isScanning = false

    private val scannedSet = ConcurrentHashMap.newKeySet<String>()
    // EPC -> InventoryId mapping for validation
    private val tagInventoryTypeMap = ConcurrentHashMap<String, Int>()
    // Set of assigned tags to prevent re-assignment in Assign Scans tab
    private val assignedTagsSet = ConcurrentHashMap.newKeySet<String>()
    // Separate movement queue dedupe set to prevent duplicate counts.
    private val movementQueuedSet = ConcurrentHashMap.newKeySet<String>()
    
    private val scannedTags = mutableStateListOf<String>()
    private val pendingAssignTags = mutableStateListOf<String>()
    private val movementScannedTags = mutableStateListOf<String>()
    private val apiLogLines = mutableStateListOf<String>()

    private val mainTabIndex = mutableIntStateOf(0)
    
    // Tracks the currently selected inventory type in the Movements tab for real-time scan validation
    private val movementSelectedInventoryId = mutableStateOf<Int?>(null)
    
    // Error state for UI popups
    private val scanErrorMessage = mutableStateOf<String?>(null)
    private val untagModeEnabled = mutableStateOf(false)

    private lateinit var tokenStore: TokenStore
    private lateinit var sparklApi: SparklApiClient

    private val scriptUrl =
        "https://script.google.com/macros/s/AKfycbyHfdxZF7JQtAL-fDWnv0yxgNVPattaZ36RvYH27zVbQMsX8d_vVhBT18SmtJ-JGBkqKQ/exec"

    private val client = OkHttpClient.Builder()
        .followRedirects(true)
        .followSslRedirects(true)
        .build()

    private val authToken = mutableStateOf<String?>(null)
    private val apiStatus = mutableStateOf("Not signed in")

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        Log.d("RFID_APP", "MainActivity onCreate started")

        tokenStore = TokenStore(applicationContext)
        sparklApi = SparklApiClient(tokenStore, client)
        authToken.value = tokenStore.bearerToken
        if (authToken.value != null) {
            val email = tokenStore.userEmail ?: "Unknown User"
            apiStatus.value = "Session restored ($email)"
            authToken.value?.let { syncTagInventoryMapFromServer(it) }
        }

        enableEdgeToEdge()

        setContent {
            SparklInventoryTheme {
                val resolvedBaseUrl = remember { mutableStateOf(sparklApi.resolvedBaseUrl()) }
                
                // Helper to trigger a UI refresh of inventory types
                val triggerRefresh = remember { mutableStateOf(0) }
                
                SparklMainScreen(
                    tabIndex = mainTabIndex.intValue,
                    onTabChange = { idx ->
                        mainTabIndex.intValue = idx
                        if (idx == 1) {
                            authToken.value?.let { syncTagInventoryMapFromServer(it) }
                        }
                    },
                    pendingAssignTags = pendingAssignTags,
                    movementScannedTags = movementScannedTags,
                    lastMovementRfid = if (movementScannedTags.isNotEmpty()) movementScannedTags.last() else "",
                    apiLog = apiLogLines,
                    authToken = authToken.value,
                    apiStatus = apiStatus.value,
                    resolvedBaseUrl = resolvedBaseUrl.value,
                    sparklApi = sparklApi,
                    initialEmail = tokenStore.userEmail ?: "",
                    initialPassword = "",
                    initialCustomBaseUrl = tokenStore.customBaseUrl.orEmpty(),
                    hasToken = authToken.value != null,
                    tagInventoryTypeMap = tagInventoryTypeMap,
                    onSaveServerUrl = { draft ->
                        tokenStore.customBaseUrl = draft.ifBlank { null }
                        resolvedBaseUrl.value = sparklApi.resolvedBaseUrl()
                        appendApiLog("Server URL saved → ${resolvedBaseUrl.value}")
                    },
                    onTestServerUrl = { draft ->
                        Thread {
                            val (ok, msg) = sparklApi.ping(draft)
                            runOnUiThread {
                                appendApiLog(if (ok) "Ping OK ($msg)" else "Ping failed: $msg")
                            }
                        }.start()
                    },
                    onUseDefaultServer = {
                        tokenStore.clearCustomBaseUrl()
                        resolvedBaseUrl.value = sparklApi.resolvedBaseUrl()
                        appendApiLog("Server URL reset → ${resolvedBaseUrl.value}")
                    },
                    onLogin = { email, password, customBase ->
                        tokenStore.customBaseUrl = customBase.ifBlank { null }
                        resolvedBaseUrl.value = sparklApi.resolvedBaseUrl()
                        Thread {
                            try {
                                val res = sparklApi.login(email, password)
                                runOnUiThread {
                                    if (res.ok && res.token != null) {
                                        tokenStore.bearerToken = res.token
                                        tokenStore.userEmail = email
                                        authToken.value = res.token
                                        apiStatus.value = "Signed in as ${res.user?.email ?: email}"
                                        appendApiLog("login OK → ${res.user?.type}")
                                        syncTagInventoryMapFromServer(res.token)
                                    } else {
                                        apiStatus.value = res.error ?: "Login failed"
                                        appendApiLog("login fail: ${res.error}")
                                    }
                                }
                            } catch (e: Exception) {
                                runOnUiThread {
                                    apiStatus.value = e.message ?: "Login error"
                                    appendApiLog("login error: ${e.message}")
                                }
                            }
                        }.start()
                    },
                    onLogout = {
                        tokenStore.clearSession()
                        authToken.value = null
                        apiStatus.value = "Signed out"
                    },
                    onRefreshInventory = refresh@{
                        val t = authToken.value ?: return@refresh
                        syncTagInventoryMapFromServer(t, updateStatus = true) { triggerRefresh.value++ }
                    },
                    onClearAll = {
                        scannedSet.clear()
                        scannedTags.clear()
                        pendingAssignTags.clear()
                        movementScannedTags.clear()
                        movementQueuedSet.clear()
                        apiLogLines.clear()
                        Log.d("RFID_APP", "Cleared scan buffers")
                    },
                    onClearMovements = {
                        movementScannedTags.clear()
                        movementQueuedSet.clear()
                    },
                    onAssignToInventoryType = assign@{ inventoryId, rfids ->
                        val t = authToken.value ?: return@assign
                        if (rfids.isEmpty()) return@assign
                        val tagsToAssign = rfids.map { it.trim().lowercase() }.distinct()
                        Thread {
                            try {
                                val res = sparklApi.batchAssign(t, inventoryId, tagsToAssign)
                                runOnUiThread {
                                    if (res.ok) {
                                        assignedTagsSet.addAll(tagsToAssign)
                                        tagsToAssign.forEach { tagInventoryTypeMap[it] = inventoryId }
                                        pendingAssignTags.clear()
                                        appendApiLog("Assigned ${res.assigned} tag(s) to inventory #$inventoryId")
                                    } else {
                                        appendApiLog("Assign failed: ${res.error}")
                                    }
                                }
                            } catch (e: Exception) {
                                runOnUiThread {
                                    appendApiLog("Assign error: ${e.message}")
                                }
                            }
                        }.start()
                    },
                    onCreateInventoryType = { categoryName, password, categoryGroup ->
                        val t = authToken.value ?: return@SparklMainScreen
                        val userEmail = tokenStore.userEmail ?: "dev@sparkl.local"
                        Thread {
                            try {
                                // 1. Double-check verify password
                                val loginRes = sparklApi.login(userEmail, password)
                                if (!loginRes.ok) {
                                    runOnUiThread { appendApiLog("Create category failed: Password check failed for $userEmail") }
                                    return@Thread
                                }

                                // 2. Call create-inventory-object (using name as label as well)
                                val res = sparklApi.createInventoryObject(t, categoryName, categoryName, categoryGroup)
                                runOnUiThread {
                                    if (res.ok) {
                                        appendApiLog("Created category: $categoryName")
                                        // Trigger a refresh to update the UI buttons
                                        triggerRefresh.value++
                                    } else {
                                        appendApiLog("Create category failed: ${res.error}")
                                    }
                                }
                            } catch (e: Exception) {
                                runOnUiThread {
                                    appendApiLog("Create category error: ${e.message}")
                                }
                            }
                        }.start()
                    },
                    onSetUntagMode = { enabled ->
                        untagModeEnabled.value = enabled
                    },
                    onDisableUntagMode = {
                        untagModeEnabled.value = false
                    },
                    untagModeEnabled = untagModeEnabled.value,
                    onVerifyPassword = { password, onResult ->
                        val userEmail = tokenStore.userEmail ?: "dev@sparkl.local"
                        Thread {
                            try {
                                val loginRes = sparklApi.login(userEmail, password)
                                runOnUiThread { 
                                    if (!loginRes.ok) appendApiLog("Verification failed for $userEmail")
                                    onResult(loginRes.ok) 
                                }
                            } catch (e: Exception) {
                                runOnUiThread { onResult(false) }
                            }
                        }.start()
                    },
                    onUnassignTags = { _, rfids ->
                        val t = authToken.value ?: return@SparklMainScreen
                        Thread {
                            try {
                                val res = sparklApi.batchUnassign(t, rfids)
                                runOnUiThread {
                                    if (res.ok) {
                                        rfids.forEach { 
                                            val key = it.trim().lowercase()
                                            tagInventoryTypeMap.remove(key)
                                            assignedTagsSet.remove(key)
                                        }
                                        pendingAssignTags.clear()
                                        appendApiLog("Unassigned ${res.assigned} tag(s) successfully")
                                    } else {
                                        appendApiLog("Unassign failed: ${res.error}")
                                    }
                                }
                            } catch (e: Exception) {
                                runOnUiThread {
                                    appendApiLog("Unassign error: ${e.message}")
                                }
                            }
                        }.start()
                    },
                    onSubmitBulkMovement = move@{ direction, inventoryId, quantity, fromId, toId, rfids, note, createdAt ->
                        val t = authToken.value ?: return@move
                        val uniqueRfids = rfids.map { it.trim().lowercase() }.distinct()
                        if (uniqueRfids.isEmpty()) return@move
                        Thread {
                            try {
                                var successCount = 0
                                var firstError: String? = null
                                uniqueRfids.forEach { rfidUid ->
                                    val res = sparklApi.recordMovement(
                                        t, direction, inventoryId, 1, fromId, toId, rfidUid, note, createdAt
                                    )
                                    if (res.ok) {
                                        successCount++
                                    } else if (firstError == null) {
                                        firstError = res.error
                                    }
                                }
                                
                                runOnUiThread {
                                    if (successCount == uniqueRfids.size) {
                                        appendApiLog("Bulk movement saved ($successCount tags, $direction)")
                                        movementScannedTags.clear()
                                        movementQueuedSet.clear()
                                        triggerRefresh.value++
                                    } else {
                                        appendApiLog("Movement partially failed: $successCount/${uniqueRfids.size} succeeded")
                                        if (!firstError.isNullOrBlank()) {
                                            Toast.makeText(this, firstError, Toast.LENGTH_LONG).show()
                                        }
                                    }
                                }
                            } catch (e: Exception) {
                                runOnUiThread {
                                    appendApiLog("Movement error: ${e.message}")
                                    Toast.makeText(this, e.message ?: "Movement error", Toast.LENGTH_LONG).show()
                                }
                            }
                        }.start()
                    },
                    movementSelectedInventoryId = movementSelectedInventoryId.value,
                    onMovementInventoryTypeChange = { id ->
                        // Prevent stale counts when switching movement category.
                        if (movementSelectedInventoryId.value != id) {
                            movementScannedTags.clear()
                            movementQueuedSet.clear()
                        }
                        movementSelectedInventoryId.value = id
                    },
                    scanErrorMessage = scanErrorMessage.value,
                    onClearScanError = { scanErrorMessage.value = null },
                    refreshTrigger = triggerRefresh.value
                )
            }
        }

        initRFID()
    }

    private fun appendApiLog(line: String) {
        val stamp = android.text.format.DateFormat.format("HH:mm:ss", java.util.Date())
        apiLogLines.add(0, "[$stamp] $line")
        if (apiLogLines.size > 40) apiLogLines.removeAt(apiLogLines.lastIndex)
    }

    /**
     * Loads tag → inventory type map from server so Stock Movement scans can validate against selected category.
     */
    private fun syncTagInventoryMapFromServer(
        token: String,
        updateStatus: Boolean = false,
        onSuccessExtra: () -> Unit = {},
    ) {
        Thread {
            try {
                val res = sparklApi.myInventory(token)
                runOnUiThread {
                    if (res.ok) {
                        val n = res.items?.size ?: 0
                        if (updateStatus) {
                            apiStatus.value = "Server inventory rows: $n"
                            appendApiLog("my-inventory OK ($n)")
                        }
                        tagInventoryTypeMap.clear()
                        res.items?.forEach { item ->
                            val key = item.rfidUid?.trim()?.lowercase()
                            if (!key.isNullOrBlank()) {
                                tagInventoryTypeMap[key] = item.inventoryId
                            }
                        }
                        onSuccessExtra()
                    } else {
                        appendApiLog("my-inventory: ${res.error}")
                        if (res.error?.contains("401") == true) {
                            tokenStore.clearSession()
                            authToken.value = null
                            apiStatus.value = "Session expired — sign in again"
                        }
                    }
                }
            } catch (e: Exception) {
                runOnUiThread {
                    appendApiLog("my-inventory error: ${e.message}")
                }
            }
        }.start()
    }

    private fun initRFID() {
        Thread {
            try {
                Log.d("RFID_APP", "Initializing RFID module...")
                rfid = B.getInstance()
                val initResult = rfid?.init(this)
                Log.d("RFID_APP", "INIT RESULT = $initResult")
                rfid?.setPower(30)
            } catch (e: Exception) {
                Log.e("RFID_APP", "INIT ERROR: ${e.message}", e)
            }
        }.start()
    }

    private fun startInventory() {
        if (isScanning) return
        isScanning = true
        Log.d("RFID_APP", "Inventory starting...")

        Thread {
            val started = rfid?.startInventoryTag()
            Log.d("RFID_APP", "startInventoryTag result = $started")

            while (isScanning) {
                var tag: UHFTAGInfo? = rfid?.readTagFromBuffer()
                while (tag != null) {
                    val epcRaw = tag.epc
                    val epc = epcRaw.trim().lowercase()
                    Log.d("RFID_APP", "Read tag: $epcRaw => normalized: $epc")

                    if (scannedSet.add(epc)) {
                        runOnUiThread {
                            scannedTags.add(epc)
                            when (mainTabIndex.intValue) {
                                0 -> {
                                    if (untagModeEnabled.value || !assignedTagsSet.contains(epc)) {
                                        pendingAssignTags.add(epc)
                                    }
                                }
                                1 -> {
                                    // Handled below with dedicated movement dedupe set.
                                }
                                else -> { }
                            }
                        }
                        sendToGoogleSheets(epc)
                    }

                    if (mainTabIndex.intValue == 1) {
                        val selectedId = movementSelectedInventoryId.value
                        val actualId = tagInventoryTypeMap[epc]
                        if (selectedId != null && actualId == selectedId && movementQueuedSet.add(epc)) {
                            runOnUiThread {
                                movementScannedTags.add(epc)
                            }
                        }
                    }
                    tag = rfid?.readTagFromBuffer()
                }
                Thread.sleep(50)
            }
        }.start()
    }

    private fun sendToGoogleSheets(epc: String) {
        if (scriptUrl.isEmpty() ||
            scriptUrl.contains("AKfycbyHfdxZF7JQtAL-fDWnv0yxgNVPattaZ36RvYH27zVbQMsX8d_vVhBT18SmtJ-JGBkqKQ/exec")
        ) {
            return
        }
        val formBody = FormBody.Builder().add("epc", epc).build()
        val request = Request.Builder().url(scriptUrl).post(formBody).build()
        client.newCall(request).enqueue(object : Callback {
            override fun onFailure(call: Call, e: IOException) {
                Log.e("GoogleSheets", "NETWORK ERROR for $epc: ${e.message}", e)
            }

            override fun onResponse(call: Call, response: Response) {
                response.close()
            }
        })
    }

    private fun stopInventory() {
        if (!isScanning) return
        Log.d("RFID_APP", "Stopping inventory...")
        isScanning = false
        Thread {
            Thread.sleep(200)
            rfid?.stopInventory()
            Log.d("RFID_APP", "Inventory stopped")
        }.start()
    }

    override fun onKeyDown(keyCode: Int, event: KeyEvent): Boolean {
        Log.d("RFID_APP", "Key down: $keyCode")
        if ((keyCode == 293 || keyCode == 280 || keyCode == 139) && event.repeatCount == 0) {
            startInventory()
            return true
        }
        return super.onKeyDown(keyCode, event)
    }

    override fun onKeyUp(keyCode: Int, event: KeyEvent): Boolean {
        if (keyCode == 293 || keyCode == 280 || keyCode == 139) {
            stopInventory()
            return true
        }
        return super.onKeyUp(keyCode, event)
    }

    override fun onDestroy() {
        super.onDestroy()
        stopInventory()
        rfid?.free()
        Log.d("RFID_APP", "RFID module freed")
    }
}
