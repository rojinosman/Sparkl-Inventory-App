package com.example.sparklinventory.data

import android.content.Context

/**
 * Persists API bearer token, user info, and optional custom base URL.
 */
class TokenStore(context: Context) {

    private val prefs = context.applicationContext.getSharedPreferences(PREFS, Context.MODE_PRIVATE)

    var bearerToken: String?
        get() = prefs.getString(KEY_TOKEN, null)
        set(value) {
            prefs.edit().apply {
                if (value == null) remove(KEY_TOKEN) else putString(KEY_TOKEN, value)
            }.apply()
        }

    var userEmail: String?
        get() = prefs.getString(KEY_EMAIL, null)
        set(value) {
            prefs.edit().apply {
                if (value == null) remove(KEY_EMAIL) else putString(KEY_EMAIL, value)
            }.apply()
        }

    /** Override [BuildConfig.SPARKL_API_BASE_URL], e.g. http://192.168.1.50:8080/ (with trailing slash). */
    var customBaseUrl: String?
        get() = prefs.getString(KEY_BASE_URL, null)
        set(value) {
            prefs.edit().apply {
                if (value.isNullOrBlank()) remove(KEY_BASE_URL) else putString(KEY_BASE_URL, value.trim())
            }.apply()
        }

    fun clearSession() {
        prefs.edit().remove(KEY_TOKEN).remove(KEY_EMAIL).apply()
    }

    /** Clears saved server URL so the app uses the Gradle default (SPARKL_API_BASE_URL) again. */
    fun clearCustomBaseUrl() {
        prefs.edit().remove(KEY_BASE_URL).apply()
    }

    companion object {
        private const val PREFS = "sparkl_auth"
        private const val KEY_TOKEN = "bearer_token"
        private const val KEY_EMAIL = "user_email"
        private const val KEY_BASE_URL = "custom_base_url"
    }
}
