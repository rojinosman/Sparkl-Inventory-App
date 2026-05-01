package com.example.sparklinventory.ui.theme

import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.lightColorScheme
import androidx.compose.runtime.Composable

private val SparklColorScheme = lightColorScheme(
    primary = SparklPink,
    onPrimary = SparklWhite,
    primaryContainer = SparklYellow,
    onPrimaryContainer = SparklDark,
    
    secondary = SparklGreen,
    onSecondary = SparklWhite,
    secondaryContainer = SparklLightGreen,
    onSecondaryContainer = SparklDark,
    
    background = SparklWhite,
    onBackground = SparklDark,
    surface = SparklWhite,
    onSurface = SparklDark,
    surfaceVariant = SparklLightGreen,
    onSurfaceVariant = SparklDark,
    
    error = SparklPink,
    onError = SparklWhite
)

@Composable
fun SparklInventoryTheme(
    content: @Composable () -> Unit
) {
    MaterialTheme(
        colorScheme = SparklColorScheme,
        typography = Typography,
        content = content
    )
}