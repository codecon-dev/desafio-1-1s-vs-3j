package com.fialho.apicodecon.api

import com.fialho.apicodecon.domain.User
import java.time.LocalDate
import java.time.OffsetDateTime

data class LoginsResponse(
    val logins: List<LoginResponse>,
    val timestamp: OffsetDateTime,
    val executionTimeMs: Long
)
