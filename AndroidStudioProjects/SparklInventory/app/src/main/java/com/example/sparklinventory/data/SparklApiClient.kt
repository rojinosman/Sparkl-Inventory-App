package com.example.sparklinventory.data

import com.google.gson.Gson
import com.google.gson.annotations.SerializedName
import okhttp3.MediaType.Companion.toMediaType
import okhttp3.OkHttpClient
import okhttp3.Request
import okhttp3.RequestBody.Companion.toRequestBody
import java.io.IOException
import java.util.concurrent.TimeUnit

private val JSON = "application/json; charset=utf-8".toMediaType()
private const val LIVE_BASE_URL = "https://bulk.sparklreusables.com/live/"

data class LoginResponse(
    val ok: Boolean = false,
    val token: String? = null,
    val error: String? = null,
    val user: LoginUser? = null,
)

data class LoginUser(
    val id: Int = 0,
    val email: String? = null,
    val type: String? = null,
    @SerializedName("company_name") val companyName: String? = null,
)

data class ScanResponse(
    val ok: Boolean = false,
    val error: String? = null,
    val inventory: ScanInventory? = null,
    @SerializedName("user_id") val userId: Int? = null,
)

data class ScanInventory(
    val id: Int = 0,
    @SerializedName("inventory_id") val inventoryObjectId: Int? = null,
    val name: String? = null,
    val label: String? = null,
    @SerializedName("rfid_uid") val rfidUid: String? = null,
)

data class MyInventoryResponse(
    val ok: Boolean = false,
    val error: String? = null,
    @SerializedName("user_id") val userId: Int? = null,
    val items: List<MyInventoryItem>? = null,
)

data class MyInventoryItem(
    @SerializedName("inventory_id") val inventoryId: Int = 0,
    @SerializedName("rfid_uid") val rfidUid: String? = null,
    @SerializedName("created_at") val createdAt: String? = null,
    val name: String? = null,
    val label: String? = null,
)

data class InventoryTypesResponse(
    val ok: Boolean = false,
    val error: String? = null,
    val items: List<InventoryTypeDto>? = null,
)

data class InventoryTypeDto(
    val id: Int = 0,
    val name: String? = null,
    val label: String? = null,
)

data class LocationsResponse(
    val ok: Boolean = false,
    val error: String? = null,
    val items: List<LocationDto>? = null,
)

data class LocationDto(
    val id: Int = 0,
    val name: String? = null,
    @SerializedName("vendor_id") val vendorId: Int? = null,
)

data class BatchAssignResponse(
    val ok: Boolean = false,
    val error: String? = null,
    val assigned: Int = 0,
    @SerializedName("inventory_id") val inventoryId: Int? = null,
)

data class MovementResponse(
    val ok: Boolean = false,
    val error: String? = null,
    val id: Int? = null,
)

data class MovementReportResponse(
    val ok: Boolean = false,
    val error: String? = null,
    val items: List<MovementReportItem>? = null,
)

data class MovementReportItem(
    val id: Int = 0,
    val direction: String? = null,
    @SerializedName("rfid_uid") val rfidUid: String? = null,
    @SerializedName("inventory_id") val inventoryId: Int? = null,
    @SerializedName("from_location_id") val fromLocationId: Int? = null,
    @SerializedName("to_location_id") val toLocationId: Int? = null,
    @SerializedName("object_label") val objectLabel: String? = null,
    val quantity: Int? = null,
    @SerializedName("created_at") val createdAt: String? = null,
    @SerializedName("from_location_name") val fromLocationName: String? = null,
    @SerializedName("to_location_name") val toLocationName: String? = null,
)

data class CreateInventoryTypeResponse(
    val ok: Boolean = false,
    val error: String? = null,
    val id: Int? = null,
)

data class MissingIncomingResponse(
    val ok: Boolean = false,
    val error: String? = null,
    @SerializedName("outstanding_count") val outstandingCount: Int = 0,
    @SerializedName("outstanding_rfids") val outstandingRfids: List<String>? = null,
)

data class MissingAgedResponse(
    val ok: Boolean = false,
    val error: String? = null,
    @SerializedName("total_missing") val totalMissing: Int = 0,
    val items: List<MissingAgedItem>? = null,
)

data class MissingAgedItem(
    @SerializedName("rfid_uid") val rfidUid: String? = null,
    @SerializedName("vendor_location_id") val vendorLocationId: Int? = null,
    @SerializedName("vendor_location_name") val vendorLocationName: String? = null,
    @SerializedName("checked_out_date") val checkedOutDate: String? = null,
    @SerializedName("days_out") val daysOut: Int? = null,
    @SerializedName("object_name") val objectName: String? = null,
    @SerializedName("object_label") val objectLabel: String? = null,
)

/**
 * Blocking calls — invoke only from a background thread.
 */
class SparklApiClient(
    private val tokenStore: TokenStore,
    client: OkHttpClient? = null,
    private val gson: Gson = Gson(),
) {

    private val http = client ?: OkHttpClient.Builder()
        .connectTimeout(10, TimeUnit.SECONDS) // Shorter timeout for ping
        .readTimeout(30, TimeUnit.SECONDS)
        .writeTimeout(30, TimeUnit.SECONDS)
        .build()

    /**
     * Active API root URL, trailing slash.
     */
    fun activeBaseUrl(formFieldCustomBase: String = ""): String {
        return LIVE_BASE_URL
    }

    fun resolvedBaseUrl(): String = activeBaseUrl("")

    fun ping(formFieldCustomBase: String): Pair<Boolean, String> {
        val base = try { activeBaseUrl(formFieldCustomBase) } catch(e: Exception) { "invalid url" }
        return try {
            val req = Request.Builder()
                .url("${base}api/v1/ping.php")
                .get()
                .build()
            http.newCall(req).execute().use { resp ->
                val body = resp.body?.string().orEmpty()
                if (resp.isSuccessful) {
                    Pair(true, "$base → HTTP ${resp.code}")
                } else {
                    Pair(false, "$base → HTTP ${resp.code} $body")
                }
            }
        } catch (e: Exception) {
            Pair(false, "$base → ${e.message ?: e.javaClass.simpleName}")
        }
    }

    @Throws(IOException::class)
    fun login(email: String, password: String): LoginResponse {
        val json = gson.toJson(mapOf("email" to email, "password" to password))
        val req = Request.Builder()
            .url("${activeBaseUrl()}api/v1/login.php")
            .post(json.toRequestBody(JSON))
            .build()
        return http.newCall(req).execute().use { resp ->
            val body = resp.body?.string().orEmpty()
            if (!resp.isSuccessful) {
                return LoginResponse(ok = false, error = "HTTP ${resp.code}: $body")
            }
            gson.fromJson(body, LoginResponse::class.java)
        }
    }

    @Throws(IOException::class)
    fun scanRfid(bearerToken: String, rfid: String): ScanResponse {
        val json = gson.toJson(mapOf("rfid" to rfid))
        val req = Request.Builder()
            .url("${activeBaseUrl()}api/v1/scan.php")
            .header("Authorization", "Bearer $bearerToken")
            .post(json.toRequestBody(JSON))
            .build()
        return http.newCall(req).execute().use { resp ->
            val body = resp.body?.string().orEmpty()
            if (!resp.isSuccessful) {
                return ScanResponse(ok = false, error = "HTTP ${resp.code}: $body")
            }
            gson.fromJson(body, ScanResponse::class.java)
        }
    }

    @Throws(IOException::class)
    fun inventoryTypes(bearerToken: String): InventoryTypesResponse {
        val req = Request.Builder()
            .url("${activeBaseUrl()}api/v1/inventory-types.php")
            .header("Authorization", "Bearer $bearerToken")
            .get()
            .build()
        return http.newCall(req).execute().use { resp ->
            val body = resp.body?.string().orEmpty()
            if (!resp.isSuccessful) {
                return InventoryTypesResponse(ok = false, error = "HTTP ${resp.code}: $body")
            }
            gson.fromJson(body, InventoryTypesResponse::class.java)
        }
    }

    @Throws(IOException::class)
    fun createInventoryType(bearerToken: String, name: String, label: String): CreateInventoryTypeResponse {
        val json = gson.toJson(mapOf("name" to name, "label" to label))
        val req = Request.Builder()
            .url("${activeBaseUrl()}api/v1/create-inventory-type.php")
            .header("Authorization", "Bearer $bearerToken")
            .post(json.toRequestBody(JSON))
            .build()
        return http.newCall(req).execute().use { resp ->
            val body = resp.body?.string().orEmpty()
            if (!resp.isSuccessful) {
                return CreateInventoryTypeResponse(ok = false, error = "HTTP ${resp.code}: $body")
            }
            gson.fromJson(body, CreateInventoryTypeResponse::class.java)
        }
    }

    @Throws(IOException::class)
    fun createInventoryObject(
        bearerToken: String,
        name: String,
        label: String,
        categoryGroup: String? = null,
    ): CreateInventoryTypeResponse {
        val payload = mutableMapOf<String, Any?>(
            "name" to name,
            "label" to label,
        )
        if (!categoryGroup.isNullOrBlank()) payload["category_group"] = categoryGroup
        val json = gson.toJson(payload)
        val req = Request.Builder()
            .url("${activeBaseUrl()}api/v1/create-inventory-object.php")
            .header("Authorization", "Bearer $bearerToken")
            .post(json.toRequestBody(JSON))
            .build()
        return http.newCall(req).execute().use { resp ->
            val body = resp.body?.string().orEmpty()
            if (!resp.isSuccessful) {
                return CreateInventoryTypeResponse(ok = false, error = "HTTP ${resp.code}: $body")
            }
            gson.fromJson(body, CreateInventoryTypeResponse::class.java)
        }
    }

    @Throws(IOException::class)
    fun locations(bearerToken: String): LocationsResponse {
        val req = Request.Builder()
            .url("${activeBaseUrl()}api/v1/locations.php")
            .header("Authorization", "Bearer $bearerToken")
            .get()
            .build()
        return http.newCall(req).execute().use { resp ->
            val body = resp.body?.string().orEmpty()
            if (!resp.isSuccessful) {
                return LocationsResponse(ok = false, error = "HTTP ${resp.code}: $body")
            }
            gson.fromJson(body, LocationsResponse::class.java)
        }
    }

    @Throws(IOException::class)
    fun batchAssign(bearerToken: String, inventoryId: Int, rfids: List<String>): BatchAssignResponse {
        val payload = mapOf(
            "inventory_id" to inventoryId,
            "rfid_uids" to rfids,
        )
        val json = gson.toJson(payload)
        val req = Request.Builder()
            .url("${activeBaseUrl()}api/v1/batch-assign.php")
            .header("Authorization", "Bearer $bearerToken")
            .post(json.toRequestBody(JSON))
            .build()
        return http.newCall(req).execute().use { resp ->
            val body = resp.body?.string().orEmpty()
            if (!resp.isSuccessful) {
                return BatchAssignResponse(ok = false, error = "HTTP ${resp.code}: $body")
            }
            gson.fromJson(body, BatchAssignResponse::class.java)
        }
    }

    @Throws(IOException::class)
    fun batchUnassign(bearerToken: String, rfids: List<String>): BatchAssignResponse {
        val payload = mapOf(
            "rfid_uids" to rfids,
        )
        val json = gson.toJson(payload)
        val req = Request.Builder()
            .url("${activeBaseUrl()}api/v1/batch-unassign.php")
            .header("Authorization", "Bearer $bearerToken")
            .post(json.toRequestBody(JSON))
            .build()
        return http.newCall(req).execute().use { resp ->
            val body = resp.body?.string().orEmpty()
            if (!resp.isSuccessful) {
                return BatchAssignResponse(ok = false, error = "HTTP ${resp.code}: $body")
            }
            gson.fromJson(body, BatchAssignResponse::class.java)
        }
    }

    @Throws(IOException::class)
    fun recordMovement(
        bearerToken: String,
        direction: String,
        inventoryId: Int,
        quantity: Int,
        fromLocationId: Int?,
        toLocationId: Int?,
        rfidUid: String?,
        note: String?,
        createdAt: String? = null,
    ): MovementResponse {
        val map = mutableMapOf<String, Any?>(
            "direction" to direction,
            "inventory_id" to inventoryId,
            "quantity" to quantity,
        )
        if (fromLocationId != null && fromLocationId > 0) {
            map["from_location_id"] = fromLocationId
        }
        if (toLocationId != null && toLocationId > 0) {
            map["to_location_id"] = toLocationId
        }
        if (!rfidUid.isNullOrBlank()) {
            map["rfid_uid"] = rfidUid
        }
        if (!note.isNullOrBlank()) {
            map["note"] = note
        }
        if (!createdAt.isNullOrBlank()) {
            map["created_at"] = createdAt
        }
        val json = gson.toJson(map)
        val req = Request.Builder()
            .url("${activeBaseUrl()}api/v1/movement.php")
            .header("Authorization", "Bearer $bearerToken")
            .post(json.toRequestBody(JSON))
            .build()
        return http.newCall(req).execute().use { resp ->
            val body = resp.body?.string().orEmpty()
            if (!resp.isSuccessful) {
                return MovementResponse(ok = false, error = "HTTP ${resp.code}: $body")
            }
            gson.fromJson(body, MovementResponse::class.java)
        }
    }

    @Throws(IOException::class)
    fun myInventory(bearerToken: String): MyInventoryResponse {
        val req = Request.Builder()
            .url("${activeBaseUrl()}api/v1/my-inventory.php")
            .header("Authorization", "Bearer $bearerToken")
            .get()
            .build()
        return http.newCall(req).execute().use { resp ->
            val body = resp.body?.string().orEmpty()
            if (!resp.isSuccessful) {
                return MyInventoryResponse(ok = false, error = "HTTP ${resp.code}: $body")
            }
            gson.fromJson(body, MyInventoryResponse::class.java)
        }
    }

    @Throws(IOException::class)
    fun missingIncoming(
        bearerToken: String,
        inventoryId: Int,
        fromLocationId: Int,
        kitchenLocationId: Int,
    ): MissingIncomingResponse {
        val payload = mapOf(
            "inventory_id" to inventoryId,
            "from_location_id" to fromLocationId,
            "kitchen_location_id" to kitchenLocationId,
        )
        val json = gson.toJson(payload)
        val req = Request.Builder()
            .url("${activeBaseUrl()}api/v1/missing-incoming.php")
            .header("Authorization", "Bearer $bearerToken")
            .post(json.toRequestBody(JSON))
            .build()
        return http.newCall(req).execute().use { resp ->
            val body = resp.body?.string().orEmpty()
            if (!resp.isSuccessful) {
                return MissingIncomingResponse(ok = false, error = "HTTP ${resp.code}: $body")
            }
            gson.fromJson(body, MissingIncomingResponse::class.java)
        }
    }

    @Throws(IOException::class)
    fun missingAged(
        bearerToken: String,
        days: Int = 9,
        locationId: Int? = null,
        objectGroup: String? = null,
    ): MissingAgedResponse {
        val payload = mutableMapOf<String, Any?>("days" to days)
        if (locationId != null && locationId > 0) payload["locationId"] = locationId
        if (!objectGroup.isNullOrBlank()) payload["objectGroup"] = objectGroup
        val json = gson.toJson(payload)
        val req = Request.Builder()
            .url("${activeBaseUrl()}api/v1/missing-aged.php")
            .header("Authorization", "Bearer $bearerToken")
            .post(json.toRequestBody(JSON))
            .build()
        return http.newCall(req).execute().use { resp ->
            val body = resp.body?.string().orEmpty()
            if (!resp.isSuccessful) {
                return MissingAgedResponse(ok = false, error = "HTTP ${resp.code}: $body")
            }
            gson.fromJson(body, MissingAgedResponse::class.java)
        }
    }

    @Throws(IOException::class)
    fun movementReport(
        bearerToken: String,
        days: Int = 30,
    ): MovementReportResponse {
        val payload = mapOf("days" to days)
        val json = gson.toJson(payload)
        val req = Request.Builder()
            .url("${activeBaseUrl()}api/v1/movement-report.php")
            .header("Authorization", "Bearer $bearerToken")
            .post(json.toRequestBody(JSON))
            .build()
        return http.newCall(req).execute().use { resp ->
            val body = resp.body?.string().orEmpty()
            if (!resp.isSuccessful) {
                return MovementReportResponse(ok = false, error = "HTTP ${resp.code}: $body")
            }
            gson.fromJson(body, MovementReportResponse::class.java)
        }
    }
}
