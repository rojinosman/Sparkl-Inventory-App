package com.example.sparklinventory.ui

import android.content.ContentValues
import android.graphics.Paint
import android.graphics.pdf.PdfDocument
import android.os.Build
import android.os.Environment
import android.provider.MediaStore
import android.widget.Toast
import androidx.compose.foundation.background
import androidx.compose.foundation.BorderStroke
import androidx.compose.foundation.layout.Arrangement
import androidx.compose.foundation.layout.Box
import androidx.compose.foundation.layout.Column
import androidx.compose.foundation.layout.ExperimentalLayoutApi
import androidx.compose.foundation.layout.FlowRow
import androidx.compose.foundation.layout.Row
import androidx.compose.foundation.layout.Spacer
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.foundation.layout.fillMaxWidth
import androidx.compose.foundation.layout.height
import androidx.compose.foundation.layout.padding
import androidx.compose.foundation.layout.size
import androidx.compose.foundation.layout.width
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.foundation.shape.RoundedCornerShape
import androidx.compose.foundation.Image
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.AssignmentInd
import androidx.compose.material.icons.filled.Info
import androidx.compose.material.icons.filled.KeyboardArrowDown
import androidx.compose.material.icons.filled.KeyboardArrowUp
import androidx.compose.material.icons.filled.Logout
import androidx.compose.material.icons.filled.DateRange
import androidx.compose.material.icons.filled.Visibility
import androidx.compose.material.icons.filled.VisibilityOff
import androidx.compose.material.icons.filled.QueryStats
import androidx.compose.material.icons.filled.SwapHoriz
import androidx.compose.material3.AlertDialog
import androidx.compose.material3.Badge
import androidx.compose.material3.Button
import androidx.compose.material3.ButtonDefaults
import androidx.compose.material3.Card
import androidx.compose.material3.CardDefaults
import androidx.compose.material3.CircularProgressIndicator
import androidx.compose.material3.DropdownMenu
import androidx.compose.material3.DropdownMenuItem
import androidx.compose.material3.DatePicker
import androidx.compose.material3.DatePickerDialog
import androidx.compose.material3.ExposedDropdownMenuBox
import androidx.compose.material3.ExposedDropdownMenuDefaults
import androidx.compose.material3.ExperimentalMaterial3Api
import androidx.compose.material3.FilterChip
import androidx.compose.material3.FilterChipDefaults
import androidx.compose.material3.HorizontalDivider
import androidx.compose.material3.Icon
import androidx.compose.material3.IconButton
import androidx.compose.material3.LinearProgressIndicator
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.OutlinedButton
import androidx.compose.material3.OutlinedTextField
import androidx.compose.material3.Scaffold
import androidx.compose.material3.Surface
import androidx.compose.material3.Tab
import androidx.compose.material3.TabRow
import androidx.compose.material3.TabRowDefaults
import androidx.compose.material3.Text
import androidx.compose.material3.TextButton
import androidx.compose.material3.TopAppBar
import androidx.compose.material3.TopAppBarDefaults
import androidx.compose.material3.rememberDatePickerState
import androidx.compose.runtime.Composable
import androidx.compose.runtime.LaunchedEffect
import androidx.compose.runtime.getValue
import androidx.compose.runtime.mutableStateOf
import androidx.compose.runtime.remember
import androidx.compose.runtime.rememberCoroutineScope
import androidx.compose.runtime.setValue
import androidx.compose.ui.platform.LocalContext
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.graphics.Color
import androidx.compose.ui.graphics.Shadow
import androidx.compose.ui.geometry.Offset
import androidx.compose.ui.layout.ContentScale
import androidx.compose.ui.text.font.FontFamily
import androidx.compose.ui.text.font.FontWeight
import androidx.compose.ui.text.input.PasswordVisualTransformation
import androidx.compose.ui.text.style.TextAlign
import androidx.compose.ui.text.TextStyle
import androidx.compose.ui.unit.dp
import androidx.compose.ui.unit.sp
import androidx.compose.ui.res.painterResource
import com.example.sparklinventory.R
import com.example.sparklinventory.data.InventoryTypeDto
import com.example.sparklinventory.data.LocationDto
import com.example.sparklinventory.data.MissingAgedItem
import com.example.sparklinventory.data.SparklApiClient
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import kotlinx.coroutines.withContext
import java.time.LocalDate
import java.time.Instant
import java.time.ZoneId
import java.time.format.DateTimeFormatter
import java.time.LocalDateTime
import com.example.sparklinventory.data.MovementReportItem

private val assignGroups = listOf("CLAMSHELLS", "CUPS", "BOWLS", "LIDS", "TRAYS", "UTENSILS", "OTHER")
private val movementGroups = listOf("CLAMSHELLS", "CUPS", "BOWLS", "LIDS", "TRAYS", "UTENSILS", "OTHER")
private val categoryButtonWidth = 136.dp
private val categoryButtonHeight = 36.dp

private fun inferGroup(type: InventoryTypeDto): String {
    val text = "${type.name.orEmpty()} ${type.label.orEmpty()}".uppercase()
    return when {
        "CLAMSHELL" in text -> "CLAMSHELLS"
        "LID" in text -> "LIDS"
        "TRAY" in text -> "TRAYS"
        "CUP" in text -> "CUPS"
        "UTENSIL" in text || "FORK" in text || "SPOON" in text || "KNIFE" in text -> "UTENSILS"
        "BOWL" in text -> "BOWLS"
        else -> "OTHER"
    }
}

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun SparklMainScreen(
    tabIndex: Int,
    onTabChange: (Int) -> Unit,
    pendingAssignTags: List<String>,
    movementScannedTags: List<String>,
    lastMovementRfid: String,
    apiLog: List<String>,
    authToken: String?,
    apiStatus: String,
    resolvedBaseUrl: String,
    sparklApi: SparklApiClient,
    initialEmail: String,
    initialPassword: String,
    initialCustomBaseUrl: String,
    hasToken: Boolean,
    tagInventoryTypeMap: Map<String, Int>,
    onSaveServerUrl: (draft: String) -> Unit,
    onTestServerUrl: (draft: String) -> Unit,
    onUseDefaultServer: () -> Unit,
    onLogin: (email: String, password: String, customBaseUrl: String) -> Unit,
    onLogout: () -> Unit,
    onRefreshInventory: () -> Unit,
    onClearAll: () -> Unit,
    onClearMovements: () -> Unit,
    onAssignToInventoryType: (inventoryId: Int, rfids: List<String>) -> Unit,
    onCreateInventoryType: (categoryName: String, password: String, categoryGroup: String?) -> Unit,
    onVerifyPassword: (password: String, onResult: (Boolean) -> Unit) -> Unit,
    onUnassignTags: (password: String, rfids: List<String>) -> Unit,
    onSetUntagMode: (Boolean) -> Unit,
    onDisableUntagMode: () -> Unit,
    untagModeEnabled: Boolean,
    onSubmitBulkMovement: (
        direction: String,
        inventoryId: Int,
        quantity: Int,
        fromLocationId: Int?,
        toLocationId: Int?,
        rfids: List<String>,
        note: String?,
        createdAt: String?,
    ) -> Unit,
    movementSelectedInventoryId: Int?,
    onMovementInventoryTypeChange: (Int?) -> Unit,
    scanErrorMessage: String?,
    onClearScanError: () -> Unit,
    refreshTrigger: Int = 0,
) {
    var showInfoDialog by remember { mutableStateOf(false) }

    if (authToken == null) {
        LoginScreen(
            initialEmail = initialEmail,
            initialPassword = initialPassword,
            initialCustomBaseUrl = initialCustomBaseUrl,
            apiStatus = apiStatus,
            onLogin = onLogin,
        )
        return
    }

    Scaffold(
        topBar = {
            Column(modifier = Modifier.background(MaterialTheme.colorScheme.primary)) {
                TopAppBar(
                    title = { Text("SPARKL INVENTORY", fontWeight = FontWeight.Black, letterSpacing = 1.sp, color = Color.White) },
                    colors = TopAppBarDefaults.topAppBarColors(containerColor = MaterialTheme.colorScheme.primary),
                    actions = {
                        IconButton(onClick = { showInfoDialog = true }) {
                            Icon(Icons.Default.Info, contentDescription = "App info", tint = Color.White)
                        }
                        IconButton(onClick = onLogout) {
                            Icon(Icons.Default.Logout, contentDescription = "Logout", tint = Color.White)
                        }
                    },
                )
                TabRow(selectedTabIndex = tabIndex) {
                    Tab(selected = tabIndex == 0, onClick = { onTabChange(0) }, icon = { Icon(Icons.Default.AssignmentInd, null) })
                    Tab(selected = tabIndex == 1, onClick = { onTabChange(1) }, icon = { Icon(Icons.Default.SwapHoriz, null) })
                    Tab(selected = tabIndex == 2, onClick = { onTabChange(2) }, icon = { Icon(Icons.Default.QueryStats, null) })
                }
            }
        },
    ) { padding ->
        Surface(modifier = Modifier.fillMaxSize().padding(padding)) {
            when (tabIndex) {
                0 -> AssignScansTab(
                    pendingTags = pendingAssignTags,
                    authToken = authToken,
                    sparklApi = sparklApi,
                    onAssign = onAssignToInventoryType,
                    onClear = onClearAll,
                    onCreateType = onCreateInventoryType,
                    onVerifyPassword = onVerifyPassword,
                    onUnassign = onUnassignTags,
                    onSetUntagMode = onSetUntagMode,
                    onDisableUntagMode = onDisableUntagMode,
                    untagModeEnabled = untagModeEnabled,
                    refreshTrigger = refreshTrigger,
                )
                1 -> MovementsTab(
                    authToken = authToken,
                    sparklApi = sparklApi,
                    movementScannedTags = movementScannedTags,
                    onClearMovements = onClearMovements,
                    onSubmit = onSubmitBulkMovement,
                    selectedInventoryId = movementSelectedInventoryId,
                    onInventoryTypeChange = onMovementInventoryTypeChange,
                    scanErrorMessage = scanErrorMessage,
                    onClearScanError = onClearScanError,
                    refreshTrigger = refreshTrigger,
                )
                2 -> MissingObjectsTab(
                    authToken = authToken,
                    sparklApi = sparklApi,
                    refreshTrigger = refreshTrigger,
                )
                else -> AssignScansTab(
                    pendingTags = pendingAssignTags,
                    authToken = authToken,
                    sparklApi = sparklApi,
                    onAssign = onAssignToInventoryType,
                    onClear = onClearAll,
                    onCreateType = onCreateInventoryType,
                    onVerifyPassword = onVerifyPassword,
                    onUnassign = onUnassignTags,
                    onSetUntagMode = onSetUntagMode,
                    onDisableUntagMode = onDisableUntagMode,
                    untagModeEnabled = untagModeEnabled,
                    refreshTrigger = refreshTrigger,
                )
            }
        }
    }

    if (showInfoDialog) {
        AlertDialog(
            onDismissRequest = { showInfoDialog = false },
            title = { Text("How to Use Tabs", fontWeight = FontWeight.Black) },
            text = {
                Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                    Text("Tag Assignment: Scan tags, choose a category item, then assign or untag queued tags.")
                    Text("Stock Movement: Pick incoming/outgoing, select category and locations, scan matching tags, then save the record.")
                    Text("Missing Objects: Filter by vendor, location, and category to view objects that have been out for 9+ days.")
                }
            },
            confirmButton = {
                TextButton(onClick = { showInfoDialog = false }) {
                    Text("OK")
                }
            }
        )
    }
}

@Composable
fun LoginScreen(
    initialEmail: String,
    initialPassword: String,
    initialCustomBaseUrl: String,
    apiStatus: String,
    onLogin: (email: String, password: String, customBaseUrl: String) -> Unit,
) {
    var email by remember { mutableStateOf(initialEmail) }
    var password by remember { mutableStateOf(initialPassword) }
    var passwordVisible by remember { mutableStateOf(false) }

    Column(modifier = Modifier.fillMaxSize().background(Color.White)) {
        Box(
            modifier = Modifier.fillMaxWidth().weight(1.1f).background(Color.White),
            contentAlignment = Alignment.Center
        ) {
            Surface(
                color = Color.Transparent,
                shape = RoundedCornerShape(24.dp),
                border = BorderStroke(0.dp, Color.Transparent),
                modifier = Modifier.padding(24.dp)
            ) {
                Image(
                    painter = painterResource(id = R.drawable.sparkl_login_logo),
                    contentDescription = "Sparkl logo",
                    modifier = Modifier
                        .padding(top = 56.dp)
                        .size(260.dp),
                    contentScale = ContentScale.Fit
                )
            }
        }

        Column(
            modifier = Modifier.fillMaxWidth().weight(1.4f).padding(32.dp),
            verticalArrangement = Arrangement.spacedBy(16.dp),
            horizontalAlignment = Alignment.CenterHorizontally
        ) {
            Text("LOG IN", fontWeight = FontWeight.Black, color = MaterialTheme.colorScheme.primary, letterSpacing = 2.sp)
            OutlinedTextField(value = email, onValueChange = { email = it }, label = { Text("Email") }, modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp))
            OutlinedTextField(
                value = password,
                onValueChange = { password = it },
                label = { Text("Password") },
                modifier = Modifier.fillMaxWidth(),
                visualTransformation = if (passwordVisible) androidx.compose.ui.text.input.VisualTransformation.None else PasswordVisualTransformation(),
                trailingIcon = {
                    IconButton(onClick = { passwordVisible = !passwordVisible }) {
                        Icon(
                            imageVector = if (passwordVisible) Icons.Default.VisibilityOff else Icons.Default.Visibility,
                            contentDescription = if (passwordVisible) "Hide password" else "Show password",
                        )
                    }
                },
                shape = RoundedCornerShape(12.dp)
            )
            
            Button(
                onClick = { onLogin(email, password, "") },
                modifier = Modifier.fillMaxWidth().height(60.dp),
                shape = RoundedCornerShape(16.dp),
                colors = ButtonDefaults.buttonColors(containerColor = MaterialTheme.colorScheme.secondary)
            ) {
                Text("SIGN IN", fontWeight = FontWeight.Black, fontSize = 20.sp)
            }

            if (apiStatus.isNotBlank()) {
                Text(apiStatus, textAlign = TextAlign.Center, style = MaterialTheme.typography.bodySmall, color = MaterialTheme.colorScheme.primary)
            }

            Spacer(Modifier.weight(1f))
        }
    }
}

@OptIn(ExperimentalMaterial3Api::class, ExperimentalLayoutApi::class)
@Composable
private fun AssignScansTab(
    pendingTags: List<String>,
    authToken: String?,
    sparklApi: SparklApiClient,
    onAssign: (inventoryId: Int, rfids: List<String>) -> Unit,
    onClear: () -> Unit,
    onCreateType: (categoryName: String, password: String, categoryGroup: String?) -> Unit,
    onVerifyPassword: (password: String, onResult: (Boolean) -> Unit) -> Unit,
    onUnassign: (password: String, rfids: List<String>) -> Unit,
    onSetUntagMode: (Boolean) -> Unit,
    onDisableUntagMode: () -> Unit,
    untagModeEnabled: Boolean,
    refreshTrigger: Int,
) {
    var types by remember { mutableStateOf<List<InventoryTypeDto>>(emptyList()) }
    var loadErr by remember { mutableStateOf<String?>(null) }
    var loading by remember { mutableStateOf(false) }
    var showUnassignDialog by remember { mutableStateOf(false) }

    LaunchedEffect(authToken, refreshTrigger) {
        val t = authToken ?: return@LaunchedEffect
        loading = true
        loadErr = null
        try {
            val res = withContext(Dispatchers.IO) { sparklApi.inventoryTypes(t) }
            types = res.items.orEmpty()
            if (!res.ok) loadErr = res.error
        } catch (e: Exception) {
            loadErr = e.message
        } finally {
            loading = false
        }
    }

    var showUnifiedDialog by remember { mutableStateOf(false) }
    var dialogStep by remember { mutableStateOf(1) } // 1: Password, 2: Name
    var storedPassword by remember { mutableStateOf("") }
    var verifyingPassword by remember { mutableStateOf(false) }
    var verificationError by remember { mutableStateOf<String?>(null) }
    var localName by remember { mutableStateOf("") }
    var localGroup by remember { mutableStateOf(assignGroups.first()) }
    var selectedGroup by remember { mutableStateOf(assignGroups.first()) }
    var showAssignCategoryButtons by remember { mutableStateOf(false) }
    val filteredTypes = remember(types, selectedGroup) {
        types.filter { inferGroup(it) == selectedGroup }
    }
    var localPass by remember { mutableStateOf("") }

    if (showUnifiedDialog) {
        AlertDialog(
            onDismissRequest = { if (!verifyingPassword) { showUnifiedDialog = false; dialogStep = 1; verificationError = null; localName = ""; localPass = "" } },
            title = { Text(if (dialogStep == 1) "Authentication Required" else "New Inventory Item", fontWeight = FontWeight.Black) },
            text = {
                Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                    if (dialogStep == 1) {
                        Text("Verify password to add items.")
                        OutlinedTextField(
                            value = localPass,
                            onValueChange = { localPass = it; verificationError = null },
                            label = { Text("Password") },
                            modifier = Modifier.fillMaxWidth(),
                            visualTransformation = PasswordVisualTransformation(),
                            isError = verificationError != null,
                            enabled = !verifyingPassword
                        )
                        if (verificationError != null) {
                            Text(verificationError!!, color = MaterialTheme.colorScheme.error, style = MaterialTheme.typography.labelSmall)
                        }
                    } else {
                        OutlinedTextField(value = localName, onValueChange = { localName = it }, label = { Text("Item Name") }, modifier = Modifier.fillMaxWidth())
                        Text("Category Group", fontWeight = FontWeight.Bold)
                        FlowRow(
                            modifier = Modifier.fillMaxWidth(),
                            horizontalArrangement = Arrangement.spacedBy(8.dp, Alignment.CenterHorizontally),
                            verticalArrangement = Arrangement.spacedBy(8.dp)
                        ) {
                            assignGroups.forEach { group ->
                                FilterChip(
                                    selected = localGroup == group,
                                    onClick = { localGroup = group },
                                    label = { Text(group) },
                                    modifier = Modifier.width(categoryButtonWidth).height(categoryButtonHeight),
                                    colors = FilterChipDefaults.filterChipColors(
                                        selectedContainerColor = Color(0xFFF8BBD0)
                                    )
                                )
                            }
                        }
                    }
                }
            },
            confirmButton = {
                Button(
                    onClick = {
                        if (dialogStep == 1) {
                            if (localPass.isNotBlank()) {
                                verifyingPassword = true
                                verificationError = null
                                onVerifyPassword(localPass) { success ->
                                    verifyingPassword = false
                                    if (success) {
                                        storedPassword = localPass
                                        dialogStep = 2
                                    } else {
                                        verificationError = "Invalid password. Try again."
                                    }
                                }
                            }
                        } else {
                            if (localName.isNotBlank()) {
                                onCreateType(localName, storedPassword, localGroup)
                                showUnifiedDialog = false
                                dialogStep = 1
                                localName = ""
                                localPass = ""
                            }
                        }
                    },
                    enabled = !verifyingPassword
                ) {
                    if (verifyingPassword) {
                        CircularProgressIndicator(Modifier.size(18.dp), strokeWidth = 2.dp, color = MaterialTheme.colorScheme.onPrimary)
                    } else {
                        Text(if (dialogStep == 1) "NEXT" else "CREATE")
                    }
                }
            },
            dismissButton = {
                if (!verifyingPassword) {
                    TextButton(onClick = { showUnifiedDialog = false; dialogStep = 1; verificationError = null; localName = ""; localPass = "" }) { Text("CANCEL") }
                }
            }
        )
    }

    if (showUnassignDialog) {
        var password by remember { mutableStateOf("") }
        AlertDialog(
            onDismissRequest = { showUnassignDialog = false },
            title = { Text("Confirm Untag", fontWeight = FontWeight.Black) },
            text = {
                Column(verticalArrangement = Arrangement.spacedBy(8.dp)) {
                    Text("Enter your password to unassign ${pendingTags.size} tags in the queue.")
                    OutlinedTextField(
                        value = password,
                        onValueChange = { password = it },
                        label = { Text("Password") },
                        modifier = Modifier.fillMaxWidth(),
                        visualTransformation = PasswordVisualTransformation()
                    )
                }
            },
            confirmButton = {
                Button(
                    onClick = {
                        if (password.isNotBlank()) {
                            onUnassign(password, pendingTags)
                            showUnassignDialog = false
                        }
                    },
                    colors = ButtonDefaults.buttonColors(containerColor = MaterialTheme.colorScheme.error)
                ) { Text("UNTAG ALL") }
            },
            dismissButton = {
                TextButton(onClick = { showUnassignDialog = false }) { Text("CANCEL") }
            }
        )
    }

    LazyColumn(
        modifier = Modifier.fillMaxSize().padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {
        item {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text("TAG ASSIGNMENT", fontWeight = FontWeight.Black, color = MaterialTheme.colorScheme.primary, style = MaterialTheme.typography.titleMedium)
                IconButton(onClick = { showUnifiedDialog = true; dialogStep = 1 }) {
                    Icon(Icons.Default.Add, "Add Category", tint = MaterialTheme.colorScheme.primary)
                }
            }
        }

        item {
            Text("ASSIGN QUEUE TO:", fontWeight = FontWeight.Black, color = MaterialTheme.colorScheme.primary, style = MaterialTheme.typography.titleSmall)
        }

        item {
            OutlinedButton(
                onClick = { showAssignCategoryButtons = !showAssignCategoryButtons },
                modifier = Modifier.fillMaxWidth()
            ) {
                Text(if (showAssignCategoryButtons) "Hide Categories" else "Show Categories")
                Spacer(Modifier.weight(1f))
                Icon(
                    imageVector = if (showAssignCategoryButtons) Icons.Default.KeyboardArrowUp else Icons.Default.KeyboardArrowDown,
                    contentDescription = null
                )
            }
        }

        if (showAssignCategoryButtons) {
            item {
                FlowRow(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(8.dp, Alignment.CenterHorizontally),
                    verticalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    assignGroups.forEach { group ->
                        FilterChip(
                            selected = selectedGroup == group,
                            onClick = { selectedGroup = group },
                            label = { Text(group) },
                            modifier = Modifier.width(categoryButtonWidth).height(categoryButtonHeight),
                            colors = FilterChipDefaults.filterChipColors(
                                selectedContainerColor = Color(0xFFF8BBD0)
                            )
                        )
                    }
                }
            }
        }

        items(filteredTypes) { inv ->
            Button(
                onClick = { if (pendingTags.isNotEmpty()) onAssign(inv.id, pendingTags) },
                enabled = authToken != null && pendingTags.isNotEmpty() && !untagModeEnabled,
                modifier = Modifier.fillMaxWidth().height(48.dp),
                shape = RoundedCornerShape(12.dp),
                colors = ButtonDefaults.buttonColors(containerColor = MaterialTheme.colorScheme.secondary)
            ) {
                Text((inv.label ?: inv.name ?: "Item #${inv.id}").uppercase(), fontWeight = FontWeight.Black)
            }
        }

        item {
            if (!untagModeEnabled) {
                Button(
                    onClick = { showUnassignDialog = true },
                    enabled = authToken != null,
                    modifier = Modifier.fillMaxWidth().height(48.dp),
                    shape = RoundedCornerShape(12.dp),
                    colors = ButtonDefaults.buttonColors(containerColor = Color.Gray),
                ) {
                    Text("UNTAG QUEUE", fontWeight = FontWeight.Black, color = Color.White)
                }
            } else {
                Row(horizontalArrangement = Arrangement.spacedBy(12.dp), modifier = Modifier.fillMaxWidth()) {
                    Button(
                        onClick = { if (pendingTags.isNotEmpty()) onUnassign("", pendingTags) },
                        enabled = authToken != null && pendingTags.isNotEmpty(),
                        modifier = Modifier.weight(1f).height(48.dp),
                        colors = ButtonDefaults.buttonColors(containerColor = MaterialTheme.colorScheme.error),
                    ) { Text("UNTAG QUEUE", color = Color.White, fontWeight = FontWeight.Black) }
                    OutlinedButton(
                        onClick = { onDisableUntagMode() },
                        modifier = Modifier.weight(1f).height(48.dp),
                    ) { Text("TURN OFF MODE", fontWeight = FontWeight.Black) }
                }
            }
        }

        item {
            if (loading) LinearProgressIndicator(Modifier.fillMaxWidth())
            loadErr?.let { Text(it, color = MaterialTheme.colorScheme.error, fontWeight = FontWeight.Bold) }
        }

        item { HorizontalDivider(thickness = 2.dp) }

        item {
            Row(
                modifier = Modifier.fillMaxWidth(),
                verticalAlignment = Alignment.CenterVertically,
                horizontalArrangement = Arrangement.SpaceBetween
            ) {
                Row(verticalAlignment = Alignment.CenterVertically) {
                    Text("PENDING QUEUE", fontWeight = FontWeight.Black, color = MaterialTheme.colorScheme.primary, style = MaterialTheme.typography.titleSmall)
                    Spacer(Modifier.width(8.dp))
                    Badge(containerColor = MaterialTheme.colorScheme.primary) {
                        Text(pendingTags.size.toString(), color = MaterialTheme.colorScheme.onPrimary)
                    }
                }
                TextButton(onClick = onClear) {
                    Text("CLEAR", fontWeight = FontWeight.Bold)
                }
            }
        }

        item { Text("Scanned tags in queue: ${pendingTags.size}", fontWeight = FontWeight.Bold) }

    }

    if (showUnassignDialog) {
        var password by remember { mutableStateOf("") }
        AlertDialog(
            onDismissRequest = { showUnassignDialog = false },
            title = { Text("Enable Untag Mode", fontWeight = FontWeight.Black) },
            text = {
                Column {
                    Text("Enter password to enable untag mode.")
                    OutlinedTextField(value = password, onValueChange = { password = it }, visualTransformation = PasswordVisualTransformation(), label = { Text("Password") })
                }
            },
            confirmButton = {
                Button(onClick = {
                    onVerifyPassword(password) { ok ->
                        if (ok) {
                            onSetUntagMode(true)
                        }
                    }
                    showUnassignDialog = false
                }) { Text("ENABLE") }
            },
            dismissButton = { TextButton(onClick = { showUnassignDialog = false }) { Text("CANCEL") } },
        )
    }
}

@OptIn(ExperimentalMaterial3Api::class, ExperimentalLayoutApi::class)
@Composable
private fun MovementsTab(
    authToken: String?,
    sparklApi: SparklApiClient,
    movementScannedTags: List<String>,
    onClearMovements: () -> Unit,
    onSubmit: (
        direction: String,
        inventoryId: Int,
        quantity: Int,
        fromLocationId: Int?,
        toLocationId: Int?,
        rfids: List<String>,
        note: String?,
        createdAt: String?,
    ) -> Unit,
    selectedInventoryId: Int?,
    onInventoryTypeChange: (Int?) -> Unit,
    scanErrorMessage: String?,
    onClearScanError: () -> Unit,
    refreshTrigger: Int
) {
    var types by remember { mutableStateOf<List<InventoryTypeDto>>(emptyList()) }
    var locations by remember { mutableStateOf<List<LocationDto>>(emptyList()) }
    val context = LocalContext.current
    val scope = rememberCoroutineScope()
    var loading by remember { mutableStateOf(false) }
    var err by remember { mutableStateOf<String?>(null) }
    var formErr by remember { mutableStateOf<String?>(null) }
    var showReportDialog by remember { mutableStateOf(false) }
    var reportLoading by remember { mutableStateOf(false) }
    var reportItems by remember { mutableStateOf<List<MovementReportItem>>(emptyList()) }

    var incoming by remember { mutableStateOf(true) }
    var note by remember { mutableStateOf("") }
    var recordedOn by remember { mutableStateOf(LocalDate.now().format(DateTimeFormatter.ofPattern("MM-dd-yyyy"))) }
    var showDatePicker by remember { mutableStateOf(false) }
    val datePickerState = rememberDatePickerState()

    var fromExpanded by remember { mutableStateOf(false) }
    var toExpanded by remember { mutableStateOf(false) }
    var sourceVendorExpanded by remember { mutableStateOf(false) }
    var destinationVendorExpanded by remember { mutableStateOf(false) }
    var selectedMovementGroup by remember { mutableStateOf(movementGroups.first()) }
    var showMovementCategoryButtons by remember { mutableStateOf(false) }
    val filteredTypes = remember(types, selectedMovementGroup) {
        types.filter { inferGroup(it) == selectedMovementGroup }
    }
    
    var selectedFrom by remember { mutableStateOf<LocationDto?>(null) }
    var selectedTo by remember { mutableStateOf<LocationDto?>(null) }
    var selectedSourceVendorId by remember { mutableStateOf<Int?>(null) }
    var selectedDestinationVendorId by remember { mutableStateOf<Int?>(null) }
    var missingOutstanding by remember { mutableStateOf(0) }

    val kitchenLocation = locations.firstOrNull { (it.name ?: "").contains("kitchen", true) || it.vendorId == 0 }
    fun vendorLabel(vendorId: Int?): String = when (vendorId) {
        null -> "Unknown"
        4 -> "Spectrum"
        8 -> "Smith Ranch HOA"
        11 -> "City Meals"
        49 -> "Rohnert Park Summer Market"
        50 -> "Marinwood Market"
        51 -> "Lunchette"
        52 -> "City of Mountain View"
        64 -> "Marin Civic Center Farmer's Market"
        66 -> "City of Petaluma"
        67 -> "Children's Menu"
        69 -> "6th Street Playhouse"
        72 -> "San Leandro Senior Center"
        0 -> "MAIN (Kitchen)"
        else -> "Vendor #$vendorId"
    }
    val vendorIds = remember(locations) {
        locations.mapNotNull { it.vendorId }
            .filter { it > 0 }
            .distinct()
            .sorted()
    }

    var showDialog by remember { mutableStateOf(false) }
    var dialogTitle by remember { mutableStateOf("") }
    var dialogMessage by remember { mutableStateOf("") }

    LaunchedEffect(authToken, refreshTrigger) {
        val t = authToken ?: return@LaunchedEffect
        loading = true
        err = null
        try {
            val tRes = withContext(Dispatchers.IO) { sparklApi.inventoryTypes(t) }
            val lRes = withContext(Dispatchers.IO) { sparklApi.locations(t) }
            types = tRes.items.orEmpty()
            locations = lRes.items.orEmpty()
            if (!tRes.ok) err = tRes.error
            if (!lRes.ok) err = lRes.error
        } catch (e: Exception) {
            err = e.message
        } finally {
            loading = false
        }
    }

    if (showDialog) {
        AlertDialog(
            onDismissRequest = { showDialog = false },
            title = { Text(dialogTitle, fontWeight = FontWeight.Bold) },
            text = { Text(dialogMessage) },
            confirmButton = { Button(onClick = { showDialog = false }) { Text("OK") } }
        )
    }

    if (showDatePicker) {
        DatePickerDialog(
            onDismissRequest = { showDatePicker = false },
            confirmButton = {
                TextButton(
                    onClick = {
                        val millis = datePickerState.selectedDateMillis
                        if (millis != null) {
                            val picked = Instant.ofEpochMilli(millis)
                                .atZone(ZoneId.systemDefault())
                                .toLocalDate()
                            recordedOn = picked.format(DateTimeFormatter.ofPattern("MM-dd-yyyy"))
                        }
                        showDatePicker = false
                    }
                ) { Text("OK") }
            },
            dismissButton = {
                TextButton(onClick = { showDatePicker = false }) { Text("CANCEL") }
            }
        ) {
            DatePicker(state = datePickerState)
        }
    }

    LaunchedEffect(incoming, selectedInventoryId, selectedSourceVendorId, selectedFrom, selectedTo, kitchenLocation, refreshTrigger) {
        val t = authToken ?: return@LaunchedEffect
        if (!incoming) return@LaunchedEffect
        val inventoryId = selectedInventoryId ?: return@LaunchedEffect
        val fromId = selectedFrom?.id ?: return@LaunchedEffect
        val kitchenId = selectedTo?.id ?: kitchenLocation?.id ?: return@LaunchedEffect
        try {
            val res = withContext(Dispatchers.IO) { sparklApi.missingIncoming(t, inventoryId, fromId, kitchenId) }
            if (res.ok) missingOutstanding = res.outstandingCount
        } catch (_: Exception) {
        }
    }

    LaunchedEffect(incoming, kitchenLocation?.id, locations.size) {
        if (incoming) {
            // Incoming always goes to Kitchen.
            selectedTo = kitchenLocation ?: locations.firstOrNull()
        } else {
            // Outgoing always leaves from Kitchen.
            selectedFrom = kitchenLocation ?: locations.firstOrNull()
        }
    }

    LazyColumn(
        Modifier.fillMaxSize().padding(16.dp),
        verticalArrangement = Arrangement.spacedBy(16.dp)
    ) {
        item {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text("STOCK MOVEMENT", fontWeight = FontWeight.Black, color = MaterialTheme.colorScheme.primary, style = MaterialTheme.typography.titleMedium)
                OutlinedButton(
                    onClick = {
                        val t = authToken ?: return@OutlinedButton
                        showReportDialog = true
                        reportLoading = true
                        scope.launch {
                            try {
                                val res = withContext(Dispatchers.IO) { sparklApi.movementReport(t, 30) }
                                reportItems = if (res.ok) res.items.orEmpty() else emptyList()
                            } catch (_: Exception) {
                                reportItems = emptyList()
                            } finally {
                                reportLoading = false
                            }
                        }
                    },
                    shape = RoundedCornerShape(10.dp),
                    contentPadding = androidx.compose.foundation.layout.PaddingValues(horizontal = 12.dp, vertical = 4.dp)
                ) {
                    Text("REPORT", fontWeight = FontWeight.Bold)
                }
            }
        }
        item {
            Row(Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.spacedBy(12.dp)) {
                FilterChip(
                    selected = incoming,
                    onClick = { incoming = true },
                    label = { Text("INCOMING", fontWeight = FontWeight.Bold) },
                    colors = FilterChipDefaults.filterChipColors(selectedContainerColor = MaterialTheme.colorScheme.secondary)
                )
                FilterChip(
                    selected = !incoming,
                    onClick = { incoming = false },
                    label = { Text("OUTGOING", fontWeight = FontWeight.Bold) },
                    colors = FilterChipDefaults.filterChipColors(selectedContainerColor = MaterialTheme.colorScheme.secondary)
                )
            }
        }
        item { Text("PRODUCT CATEGORY", fontWeight = FontWeight.Black, color = MaterialTheme.colorScheme.primary) }
        item {
            OutlinedButton(
                onClick = { showMovementCategoryButtons = !showMovementCategoryButtons },
                modifier = Modifier.fillMaxWidth()
            ) {
                Text(if (showMovementCategoryButtons) "Hide Categories" else "Show Categories")
                Spacer(Modifier.weight(1f))
                Icon(
                    imageVector = if (showMovementCategoryButtons) Icons.Default.KeyboardArrowUp else Icons.Default.KeyboardArrowDown,
                    contentDescription = null
                )
            }
        }
        if (showMovementCategoryButtons) {
            item {
                FlowRow(
                    modifier = Modifier.fillMaxWidth(),
                    horizontalArrangement = Arrangement.spacedBy(8.dp, Alignment.CenterHorizontally),
                    verticalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    movementGroups.forEach { group ->
                        FilterChip(
                            selected = selectedMovementGroup == group,
                            onClick = { selectedMovementGroup = group },
                            label = { Text(group) },
                            modifier = Modifier.width(categoryButtonWidth).height(categoryButtonHeight),
                            colors = FilterChipDefaults.filterChipColors(
                                selectedContainerColor = Color(0xFFF8BBD0)
                            )
                        )
                    }
                }
            }
        }
        items(filteredTypes) { inv ->
            Button(
                onClick = { onInventoryTypeChange(inv.id) },
                modifier = Modifier.fillMaxWidth().height(44.dp),
                shape = RoundedCornerShape(10.dp),
                colors = ButtonDefaults.buttonColors(
                    containerColor = if (selectedInventoryId == inv.id) Color(0xFFF8BBD0) else MaterialTheme.colorScheme.secondary
                )
            ) {
                Text((inv.label ?: inv.name ?: "#${inv.id}").uppercase(), fontWeight = FontWeight.Bold)
            }
        }
        item {
            OutlinedTextField(
                value = recordedOn,
                onValueChange = { },
                readOnly = true,
                label = { Text("Recorded On (MM-DD-YYYY)") },
                modifier = Modifier.fillMaxWidth(),
                trailingIcon = {
                    IconButton(onClick = { showDatePicker = true }) {
                        Icon(Icons.Default.DateRange, contentDescription = "Choose date")
                    }
                }
            )
        }

        if (incoming) {
            item {
                ExposedDropdownMenuBox(expanded = toExpanded, onExpandedChange = { toExpanded = !toExpanded }) {
                    OutlinedTextField(value = selectedTo?.name ?: "", onValueChange = {}, readOnly = true, label = { Text("Destination Location *") }, modifier = Modifier.fillMaxWidth().menuAnchor(), shape = RoundedCornerShape(12.dp), trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(toExpanded) })
                    DropdownMenu(expanded = toExpanded, onDismissRequest = { toExpanded = false }) {
                        val destinationLocations = listOfNotNull(kitchenLocation)
                        destinationLocations.forEach { loc ->
                            DropdownMenuItem(text = { Text(loc.name ?: "#${loc.id}") }, onClick = { selectedTo = loc; toExpanded = false })
                        }
                    }
                }
            }
            item {
                ExposedDropdownMenuBox(
                    expanded = sourceVendorExpanded,
                    onExpandedChange = { sourceVendorExpanded = !sourceVendorExpanded }
                ) {
                    OutlinedTextField(
                        value = selectedSourceVendorId?.let { vendorLabel(it) } ?: "All Vendors",
                        onValueChange = {},
                        readOnly = true,
                        label = { Text("Source Vendor ID") },
                        modifier = Modifier.fillMaxWidth().menuAnchor(),
                        shape = RoundedCornerShape(12.dp),
                        trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(sourceVendorExpanded) }
                    )
                    DropdownMenu(
                        expanded = sourceVendorExpanded,
                        onDismissRequest = { sourceVendorExpanded = false }
                    ) {
                        DropdownMenuItem(
                            text = { Text("All Vendors") },
                            onClick = {
                                selectedSourceVendorId = null
                                sourceVendorExpanded = false
                                selectedFrom = null
                            }
                        )
                        vendorIds.forEach { vid ->
                            DropdownMenuItem(
                                text = { Text(vendorLabel(vid)) },
                                onClick = {
                                    selectedSourceVendorId = vid
                                    sourceVendorExpanded = false
                                    selectedFrom = null
                                }
                            )
                        }
                    }
                }
            }
            item {
                ExposedDropdownMenuBox(expanded = fromExpanded, onExpandedChange = { fromExpanded = !fromExpanded }) {
                    OutlinedTextField(value = selectedFrom?.name ?: "", onValueChange = {}, readOnly = true, label = { Text("Source Location *") }, modifier = Modifier.fillMaxWidth().menuAnchor(), shape = RoundedCornerShape(12.dp), trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(fromExpanded) })
                    DropdownMenu(expanded = fromExpanded, onDismissRequest = { fromExpanded = false }) {
                        val sourceLocations = locations.filter { selectedSourceVendorId == null || it.vendorId == selectedSourceVendorId }
                        sourceLocations.forEach { loc ->
                            DropdownMenuItem(text = { Text(loc.name ?: "#${loc.id}") }, onClick = { selectedFrom = loc; fromExpanded = false })
                        }
                    }
                }
            }
        } else {
            item {
                ExposedDropdownMenuBox(expanded = fromExpanded, onExpandedChange = { fromExpanded = !fromExpanded }) {
                    OutlinedTextField(value = selectedFrom?.name ?: "", onValueChange = {}, readOnly = true, label = { Text("Source Location *") }, modifier = Modifier.fillMaxWidth().menuAnchor(), shape = RoundedCornerShape(12.dp), trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(fromExpanded) })
                    DropdownMenu(expanded = fromExpanded, onDismissRequest = { fromExpanded = false }) {
                        val sourceLocations = listOfNotNull(kitchenLocation)
                        sourceLocations.forEach { loc ->
                            DropdownMenuItem(text = { Text(loc.name ?: "#${loc.id}") }, onClick = { selectedFrom = loc; fromExpanded = false })
                        }
                    }
                }
            }
            item {
                ExposedDropdownMenuBox(
                    expanded = destinationVendorExpanded,
                    onExpandedChange = { destinationVendorExpanded = !destinationVendorExpanded }
                ) {
                    OutlinedTextField(
                        value = selectedDestinationVendorId?.let { vendorLabel(it) } ?: "All Vendors",
                        onValueChange = {},
                        readOnly = true,
                        label = { Text("Destination Vendor ID") },
                        modifier = Modifier.fillMaxWidth().menuAnchor(),
                        shape = RoundedCornerShape(12.dp),
                        trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(destinationVendorExpanded) }
                    )
                    DropdownMenu(
                        expanded = destinationVendorExpanded,
                        onDismissRequest = { destinationVendorExpanded = false }
                    ) {
                        DropdownMenuItem(
                            text = { Text("All Vendors") },
                            onClick = {
                                selectedDestinationVendorId = null
                                destinationVendorExpanded = false
                                selectedTo = null
                            }
                        )
                        vendorIds.forEach { vid ->
                            DropdownMenuItem(
                                text = { Text(vendorLabel(vid)) },
                                onClick = {
                                    selectedDestinationVendorId = vid
                                    destinationVendorExpanded = false
                                    selectedTo = null
                                }
                            )
                        }
                    }
                }
            }
            item {
                ExposedDropdownMenuBox(expanded = toExpanded, onExpandedChange = { toExpanded = !toExpanded }) {
                    OutlinedTextField(value = selectedTo?.name ?: "", onValueChange = {}, readOnly = true, label = { Text("Destination Location *") }, modifier = Modifier.fillMaxWidth().menuAnchor(), shape = RoundedCornerShape(12.dp), trailingIcon = { ExposedDropdownMenuDefaults.TrailingIcon(toExpanded) })
                    DropdownMenu(expanded = toExpanded, onDismissRequest = { toExpanded = false }) {
                        val destinationLocations = locations.filter { selectedDestinationVendorId == null || it.vendorId == selectedDestinationVendorId }
                        destinationLocations.forEach { loc ->
                            DropdownMenuItem(text = { Text(loc.name ?: "#${loc.id}") }, onClick = { selectedTo = loc; toExpanded = false })
                        }
                    }
                }
            }
        }
        item {
            Row(modifier = Modifier.fillMaxWidth(), horizontalArrangement = Arrangement.SpaceBetween, verticalAlignment = Alignment.CenterVertically) {
                Text("SCANNED TAGS", fontWeight = FontWeight.Black, color = MaterialTheme.colorScheme.primary, style = MaterialTheme.typography.titleSmall)
                TextButton(onClick = onClearMovements) { Text("CLEAR", fontWeight = FontWeight.Bold) }
            }
            Surface(color = MaterialTheme.colorScheme.primaryContainer.copy(alpha = 0.5f), shape = RoundedCornerShape(12.dp), modifier = Modifier.fillMaxWidth()) {
                Column(Modifier.padding(12.dp)) {
                    if (incoming) {
                        val remaining = (missingOutstanding - movementScannedTags.size).coerceAtLeast(0)
                        if (selectedFrom != null) {
                            Text("Checked Out at Source: $missingOutstanding", fontWeight = FontWeight.Bold)
                            Text("Remaining to Scan In: $remaining", fontWeight = FontWeight.Bold)
                        } else {
                            Text("Select Source Location to load checked-out count.")
                        }
                    } else {
                        Text("Count: ${movementScannedTags.size}", fontWeight = FontWeight.Bold)
                    }
                }
            }
        }
        item {
            Button(
                onClick = {
                    if (movementScannedTags.isEmpty()) { showDialog = true; dialogTitle = "No Tags"; dialogMessage = "Scan at least one tag."; return@Button }
                    onSubmit(
                        if (incoming) "in" else "out",
                        selectedInventoryId ?: 0,
                        movementScannedTags.size,
                        selectedFrom?.id,
                        selectedTo?.id,
                        movementScannedTags,
                        note.ifBlank { null },
                        recordedOn.ifBlank { null },
                    )
                },
                enabled = authToken != null && selectedInventoryId != null,
                modifier = Modifier.fillMaxWidth().height(56.dp),
                shape = RoundedCornerShape(12.dp),
                colors = ButtonDefaults.buttonColors(containerColor = MaterialTheme.colorScheme.secondary)
            ) { Text("SAVE RECORD", fontWeight = FontWeight.Black) }
        }
    }

    if (showReportDialog) {
        AlertDialog(
            onDismissRequest = { showReportDialog = false },
            title = { Text("Stock Movement Report", fontWeight = FontWeight.Black) },
            text = {
                if (reportLoading) {
                    Text("Loading movement data...")
                } else {
                    Text("Download a PDF report for incoming and outgoing inventory movements (last 30 days).")
                }
            },
            confirmButton = {
                Button(
                    onClick = {
                        val uri = exportMovementReportPdf(context, reportItems, locations)
                        if (uri != null) {
                            Toast.makeText(context, "Report downloaded to Downloads.", Toast.LENGTH_LONG).show()
                        } else {
                            Toast.makeText(context, "Failed to create movement report PDF.", Toast.LENGTH_LONG).show()
                        }
                        showReportDialog = false
                    },
                    enabled = !reportLoading && reportItems.isNotEmpty()
                ) {
                    Text("DOWNLOAD PDF")
                }
            },
            dismissButton = {
                TextButton(onClick = { showReportDialog = false }) {
                    Text("CANCEL")
                }
            }
        )
    }
}

@OptIn(ExperimentalMaterial3Api::class, ExperimentalLayoutApi::class)
@Composable
private fun MissingObjectsTab(
    authToken: String?,
    sparklApi: SparklApiClient,
    refreshTrigger: Int,
) {
    val context = LocalContext.current
    var items by remember { mutableStateOf<List<MissingAgedItem>>(emptyList()) }
    var locations by remember { mutableStateOf<List<LocationDto>>(emptyList()) }
    var loading by remember { mutableStateOf(false) }
    var selectedLocation by remember { mutableStateOf<Int?>(null) }
    var selectedVendorId by remember { mutableStateOf<Int?>(null) }
    var selectedGroup by remember { mutableStateOf<String?>(null) }
    var vendorExpanded by remember { mutableStateOf(false) }
    var locationExpanded by remember { mutableStateOf(false) }
    var showReportDialog by remember { mutableStateOf(false) }
    var showMissingCategoryButtons by remember { mutableStateOf(false) }
    val vendorIds = remember(locations) {
        locations.mapNotNull { it.vendorId }
            .filter { it > 0 }
            .distinct()
            .sorted()
    }
    fun vendorLabel(vendorId: Int?): String = when (vendorId) {
        null -> "All Vendors"
        4 -> "Spectrum"
        8 -> "Smith Ranch HOA"
        11 -> "City Meals"
        49 -> "Rohnert Park Summer Market"
        50 -> "Marinwood Market"
        51 -> "Lunchette"
        52 -> "City of Mountain View"
        64 -> "Marin Civic Center Farmer's Market"
        66 -> "City of Petaluma"
        67 -> "Children's Menu"
        69 -> "6th Street Playhouse"
        72 -> "San Leandro Senior Center"
        else -> "Vendor #$vendorId"
    }

    LaunchedEffect(authToken, refreshTrigger, selectedLocation, selectedGroup) {
        val t = authToken ?: return@LaunchedEffect
        loading = true
        try {
            val locRes = withContext(Dispatchers.IO) { sparklApi.locations(t) }
            locations = locRes.items.orEmpty()
            val res = withContext(Dispatchers.IO) { sparklApi.missingAged(t, 9, selectedLocation, selectedGroup) }
            if (res.ok) items = res.items.orEmpty()
        } catch (_: Exception) {
        } finally {
            loading = false
        }
    }

    LazyColumn(Modifier.fillMaxSize().padding(16.dp), verticalArrangement = Arrangement.spacedBy(12.dp)) {
        item {
            Row(
                modifier = Modifier.fillMaxWidth(),
                horizontalArrangement = Arrangement.SpaceBetween,
                verticalAlignment = Alignment.CenterVertically
            ) {
                Text("MISSING OBJECTS (9+ DAYS)", fontWeight = FontWeight.Black, color = MaterialTheme.colorScheme.primary)
                OutlinedButton(
                    onClick = { showReportDialog = true },
                    shape = RoundedCornerShape(10.dp),
                    contentPadding = androidx.compose.foundation.layout.PaddingValues(horizontal = 12.dp, vertical = 4.dp)
                ) {
                    Text("REPORT", fontWeight = FontWeight.Bold)
                }
            }
        }
        item {
            ExposedDropdownMenuBox(expanded = vendorExpanded, onExpandedChange = { vendorExpanded = !vendorExpanded }) {
                OutlinedTextField(
                    value = vendorLabel(selectedVendorId),
                    onValueChange = {},
                    readOnly = true,
                    label = { Text("Vendor ID Filter") },
                    modifier = Modifier.fillMaxWidth().menuAnchor(),
                )
                DropdownMenu(expanded = vendorExpanded, onDismissRequest = { vendorExpanded = false }) {
                    DropdownMenuItem(
                        text = { Text("All Vendors") },
                        onClick = {
                            selectedVendorId = null
                            selectedLocation = null
                            vendorExpanded = false
                        }
                    )
                    vendorIds.forEach { vid ->
                        DropdownMenuItem(
                            text = { Text(vendorLabel(vid)) },
                            onClick = {
                                selectedVendorId = vid
                                selectedLocation = null
                                vendorExpanded = false
                            }
                        )
                    }
                }
            }
        }
        item {
            ExposedDropdownMenuBox(expanded = locationExpanded, onExpandedChange = { locationExpanded = !locationExpanded }) {
                OutlinedTextField(
                    value = locations.firstOrNull { it.id == selectedLocation }?.name ?: "All Vendors",
                    onValueChange = {},
                    readOnly = true,
                    label = { Text("Vendor Filter") },
                    modifier = Modifier.fillMaxWidth().menuAnchor(),
                )
                DropdownMenu(expanded = locationExpanded, onDismissRequest = { locationExpanded = false }) {
                    DropdownMenuItem(text = { Text("All Vendors") }, onClick = { selectedLocation = null; locationExpanded = false })
                    val filteredLocations = locations.filter { selectedVendorId == null || it.vendorId == selectedVendorId }
                    filteredLocations.forEach { loc ->
                        DropdownMenuItem(text = { Text(loc.name ?: "#${loc.id}") }, onClick = { selectedLocation = loc.id; locationExpanded = false })
                    }
                }
            }
        }
        item {
            OutlinedButton(
                onClick = { showMissingCategoryButtons = !showMissingCategoryButtons },
                modifier = Modifier.fillMaxWidth()
            ) {
                Text(if (showMissingCategoryButtons) "Hide Categories" else "Show Categories")
                Spacer(Modifier.weight(1f))
                Icon(
                    imageVector = if (showMissingCategoryButtons) Icons.Default.KeyboardArrowUp else Icons.Default.KeyboardArrowDown,
                    contentDescription = null
                )
            }
        }
        if (showMissingCategoryButtons) {
            item {
                FlowRow(
                    horizontalArrangement = Arrangement.spacedBy(8.dp, Alignment.CenterHorizontally),
                    verticalArrangement = Arrangement.spacedBy(8.dp),
                    modifier = Modifier.fillMaxWidth()
                ) {
                    movementGroups.forEach { group ->
                        FilterChip(
                            selected = selectedGroup == group,
                            onClick = { selectedGroup = if (selectedGroup == group) null else group },
                            label = { Text(group) },
                            modifier = Modifier.width(categoryButtonWidth).height(categoryButtonHeight),
                        )
                    }
                }
            }
        }
        item { if (loading) LinearProgressIndicator(Modifier.fillMaxWidth()) }
        item { Text("Running Missing Total: ${items.size}", fontWeight = FontWeight.Bold) }
        items(items) { item ->
            Card(Modifier.fillMaxWidth(), colors = CardDefaults.cardColors(containerColor = MaterialTheme.colorScheme.primaryContainer.copy(alpha = 0.4f))) {
                Column(Modifier.padding(12.dp)) {
                    val vendorText = item.vendorLocationName
                        ?.takeIf { it.isNotBlank() }
                        ?: item.vendorLocationId?.let { id -> locations.firstOrNull { it.id == id }?.name }
                        ?: "Unknown destination"
                    Text(item.objectLabel ?: item.objectName ?: "Unknown", fontWeight = FontWeight.Bold)
                    Text("Vendor: $vendorText")
                    Text("Checked Out: ${item.checkedOutDate ?: "-"}")
                    Text("Days Out: ${item.daysOut ?: 0}")
                }
            }
        }
    }

    if (showReportDialog) {
        AlertDialog(
            onDismissRequest = { showReportDialog = false },
            title = { Text("Missing Objects Report", fontWeight = FontWeight.Black) },
            text = { Text("Download a PDF report of the currently filtered missing objects.") },
            confirmButton = {
                Button(
                    onClick = {
                        val uri = exportMissingObjectsReportPdf(context, items, locations)
                        if (uri != null) {
                            Toast.makeText(context, "Report downloaded to Downloads.", Toast.LENGTH_LONG).show()
                        } else {
                            Toast.makeText(context, "Failed to create report PDF.", Toast.LENGTH_LONG).show()
                        }
                        showReportDialog = false
                    },
                    enabled = items.isNotEmpty()
                ) {
                    Text("DOWNLOAD PDF")
                }
            },
            dismissButton = {
                TextButton(onClick = { showReportDialog = false }) {
                    Text("CANCEL")
                }
            }
        )
    }
}

private fun exportMissingObjectsReportPdf(
    context: android.content.Context,
    items: List<MissingAgedItem>,
    locations: List<LocationDto>
): android.net.Uri? {
    if (items.isEmpty()) return null

    val pageWidth = 595 // A4-ish width in points
    val pageHeight = 842
    val margin = 32
    val lineHeight = 18

    val titlePaint = Paint().apply {
        color = android.graphics.Color.BLACK
        textSize = 16f
        isFakeBoldText = true
    }
    val headerPaint = Paint().apply {
        color = android.graphics.Color.BLACK
        textSize = 11f
        isFakeBoldText = true
    }
    val bodyPaint = Paint().apply {
        color = android.graphics.Color.BLACK
        textSize = 10f
    }

    val pdf = PdfDocument()
    var pageNumber = 1
    var y = margin + 36

    fun newPage(): Pair<PdfDocument.Page, android.graphics.Canvas> {
        val pageInfo = PdfDocument.PageInfo.Builder(pageWidth, pageHeight, pageNumber).create()
        val page = pdf.startPage(pageInfo)
        val canvas = page.canvas
        canvas.drawText("Missing Objects Report (9+ Days)", margin.toFloat(), (margin + 10).toFloat(), titlePaint)
        canvas.drawText("Generated: ${LocalDateTime.now().format(DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm"))}", margin.toFloat(), (margin + 26).toFloat(), bodyPaint)
        canvas.drawText("Item", margin.toFloat(), y.toFloat(), headerPaint)
        canvas.drawText("Vendor/Location", (margin + 190).toFloat(), y.toFloat(), headerPaint)
        canvas.drawText("Checked Out", (margin + 390).toFloat(), y.toFloat(), headerPaint)
        canvas.drawText("Days", (margin + 500).toFloat(), y.toFloat(), headerPaint)
        y += lineHeight
        return page to canvas
    }

    var (page, canvas) = newPage()

    for (item in items) {
        if (y > pageHeight - margin) {
            pdf.finishPage(page)
            pageNumber += 1
            y = margin + 36
            val next = newPage()
            page = next.first
            canvas = next.second
        }

        val name = (item.objectLabel ?: item.objectName ?: "Unknown").take(30)
        val vendor = (
            item.vendorLocationName
                ?.takeIf { it.isNotBlank() }
                ?: item.vendorLocationId?.let { id -> locations.firstOrNull { it.id == id }?.name }
                ?: "Unknown destination"
            ).take(30)
        val checkedOut = (item.checkedOutDate ?: "-").take(14)
        val days = (item.daysOut ?: 0).toString()

        canvas.drawText(name, margin.toFloat(), y.toFloat(), bodyPaint)
        canvas.drawText(vendor, (margin + 190).toFloat(), y.toFloat(), bodyPaint)
        canvas.drawText(checkedOut, (margin + 390).toFloat(), y.toFloat(), bodyPaint)
        canvas.drawText(days, (margin + 500).toFloat(), y.toFloat(), bodyPaint)
        y += lineHeight
    }

    pdf.finishPage(page)

    return try {
        val fileName = "missing_objects_report_${LocalDateTime.now().format(DateTimeFormatter.ofPattern("yyyyMMdd_HHmmss"))}.pdf"
        val resolver = context.contentResolver
        val collection = MediaStore.Downloads.EXTERNAL_CONTENT_URI
        val contentValues = ContentValues().apply {
            put(MediaStore.MediaColumns.DISPLAY_NAME, fileName)
            put(MediaStore.MediaColumns.MIME_TYPE, "application/pdf")
            put(MediaStore.MediaColumns.RELATIVE_PATH, Environment.DIRECTORY_DOWNLOADS)
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
                put(MediaStore.MediaColumns.IS_PENDING, 1)
            }
        }

        val uri = resolver.insert(collection, contentValues) ?: return null
        resolver.openOutputStream(uri)?.use { out ->
            pdf.writeTo(out)
        } ?: return null

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            contentValues.clear()
            contentValues.put(MediaStore.MediaColumns.IS_PENDING, 0)
            resolver.update(uri, contentValues, null, null)
        }
        uri
    } catch (_: Exception) {
        null
    } finally {
        pdf.close()
    }
}

private fun exportMovementReportPdf(
    context: android.content.Context,
    items: List<MovementReportItem>,
    locations: List<LocationDto>
): android.net.Uri? {
    if (items.isEmpty()) return null

    val pageWidth = 595
    val pageHeight = 842
    val margin = 28
    val lineHeight = 17

    val titlePaint = Paint().apply {
        color = android.graphics.Color.BLACK
        textSize = 15f
        isFakeBoldText = true
    }
    val headerPaint = Paint().apply {
        color = android.graphics.Color.BLACK
        textSize = 10f
        isFakeBoldText = true
    }
    val bodyPaint = Paint().apply {
        color = android.graphics.Color.BLACK
        textSize = 9f
    }

    val pdf = PdfDocument()
    var pageNumber = 1
    var y = margin + 36

    fun newPage(): Pair<PdfDocument.Page, android.graphics.Canvas> {
        val pageInfo = PdfDocument.PageInfo.Builder(pageWidth, pageHeight, pageNumber).create()
        val page = pdf.startPage(pageInfo)
        val canvas = page.canvas
        canvas.drawText("Stock Movement Report (Incoming + Outgoing)", margin.toFloat(), (margin + 10).toFloat(), titlePaint)
        canvas.drawText("Generated: ${LocalDateTime.now().format(DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm"))}", margin.toFloat(), (margin + 24).toFloat(), bodyPaint)
        canvas.drawText("Date", margin.toFloat(), y.toFloat(), headerPaint)
        canvas.drawText("Dir", (margin + 98).toFloat(), y.toFloat(), headerPaint)
        canvas.drawText("Item", (margin + 136).toFloat(), y.toFloat(), headerPaint)
        canvas.drawText("From", (margin + 290).toFloat(), y.toFloat(), headerPaint)
        canvas.drawText("To", (margin + 400).toFloat(), y.toFloat(), headerPaint)
        canvas.drawText("Qty", (margin + 510).toFloat(), y.toFloat(), headerPaint)
        y += lineHeight
        return page to canvas
    }

    var (page, canvas) = newPage()

    for (item in items) {
        if (y > pageHeight - margin) {
            pdf.finishPage(page)
            pageNumber += 1
            y = margin + 36
            val next = newPage()
            page = next.first
            canvas = next.second
        }

        val created = (item.createdAt ?: "-").take(16)
        val direction = when ((item.direction ?: "").lowercase()) {
            "in", "incoming" -> "IN"
            "out", "outgoing" -> "OUT"
            else -> item.direction ?: "-"
        }
        val itemLabel = (item.objectLabel ?: "Inventory #${item.inventoryId ?: 0}").take(24)
        val fromName = (
            item.fromLocationName
                ?.takeIf { it.isNotBlank() }
                ?: item.fromLocationId?.let { id -> locations.firstOrNull { it.id == id }?.name }
                ?: "Unknown"
            ).take(16)
        val toName = (
            item.toLocationName
                ?.takeIf { it.isNotBlank() }
                ?: item.toLocationId?.let { id -> locations.firstOrNull { it.id == id }?.name }
                ?: "Unknown"
            ).take(16)
        val qty = (item.quantity ?: 0).toString()

        canvas.drawText(created, margin.toFloat(), y.toFloat(), bodyPaint)
        canvas.drawText(direction, (margin + 98).toFloat(), y.toFloat(), bodyPaint)
        canvas.drawText(itemLabel, (margin + 136).toFloat(), y.toFloat(), bodyPaint)
        canvas.drawText(fromName, (margin + 290).toFloat(), y.toFloat(), bodyPaint)
        canvas.drawText(toName, (margin + 400).toFloat(), y.toFloat(), bodyPaint)
        canvas.drawText(qty, (margin + 510).toFloat(), y.toFloat(), bodyPaint)
        y += lineHeight
    }

    pdf.finishPage(page)

    return try {
        val fileName = "stock_movement_report_${LocalDateTime.now().format(DateTimeFormatter.ofPattern("yyyyMMdd_HHmmss"))}.pdf"
        val resolver = context.contentResolver
        val collection = MediaStore.Downloads.EXTERNAL_CONTENT_URI
        val contentValues = ContentValues().apply {
            put(MediaStore.MediaColumns.DISPLAY_NAME, fileName)
            put(MediaStore.MediaColumns.MIME_TYPE, "application/pdf")
            put(MediaStore.MediaColumns.RELATIVE_PATH, Environment.DIRECTORY_DOWNLOADS)
            if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
                put(MediaStore.MediaColumns.IS_PENDING, 1)
            }
        }

        val uri = resolver.insert(collection, contentValues) ?: return null
        resolver.openOutputStream(uri)?.use { out ->
            pdf.writeTo(out)
        } ?: return null

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.Q) {
            contentValues.clear()
            contentValues.put(MediaStore.MediaColumns.IS_PENDING, 0)
            resolver.update(uri, contentValues, null, null)
        }
        uri
    } catch (_: Exception) {
        null
    } finally {
        pdf.close()
    }
}

@Composable
private fun SettingsTabContent(
    initialEmail: String,
    initialPassword: String,
    apiStatus: String,
    resolvedBaseUrl: String,
    hasToken: Boolean,
    apiLog: List<String>,
    onLogin: (email: String, password: String, customBaseUrl: String) -> Unit,
    onLogout: () -> Unit,
    onRefreshInventory: () -> Unit,
) {
    var email by remember { mutableStateOf(initialEmail) }
    var password by remember { mutableStateOf(initialPassword) }

    LazyColumn(Modifier.fillMaxSize().padding(16.dp), verticalArrangement = Arrangement.spacedBy(16.dp)) {
        item { Text("APP SETTINGS", fontWeight = FontWeight.Black, color = MaterialTheme.colorScheme.primary, style = MaterialTheme.typography.titleMedium) }
        item {
            Surface(color = MaterialTheme.colorScheme.secondaryContainer, shape = RoundedCornerShape(12.dp), modifier = Modifier.fillMaxWidth()) {
                Column(Modifier.padding(16.dp)) {
                    Text("STATUS: $apiStatus", fontWeight = FontWeight.Black, style = MaterialTheme.typography.bodySmall)
                    Text("URL: $resolvedBaseUrl", style = MaterialTheme.typography.bodySmall)
                }
            }
        }
        item { HorizontalDivider(thickness = 2.dp) }
        item {
            OutlinedTextField(email, { email = it }, label = { Text("Email") }, modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp))
            OutlinedTextField(password, { password = it }, label = { Text("Password") }, modifier = Modifier.fillMaxWidth(), shape = RoundedCornerShape(12.dp), visualTransformation = PasswordVisualTransformation())
            Row(Modifier.fillMaxWidth().padding(top = 8.dp), horizontalArrangement = Arrangement.spacedBy(8.dp)) {
                Button(onClick = { onLogin(email, password, "") }, Modifier.weight(1f)) { Text("LOGIN") }
                OutlinedButton(onClick = onLogout, enabled = hasToken, modifier = Modifier.weight(1f)) { Text("LOGOUT") }
            }
            Button(onClick = onRefreshInventory, enabled = hasToken, modifier = Modifier.fillMaxWidth().padding(top = 8.dp)) { Text("REFRESH DATA") }
        }
    }
}
