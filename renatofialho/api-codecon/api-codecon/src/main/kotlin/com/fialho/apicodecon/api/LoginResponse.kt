package com.fialho.apicodecon.api

import com.fialho.apicodecon.domain.User
import java.time.LocalDate
import java.time.OffsetDateTime

data class LoginResponse(
    val date: LocalDate,
    val total: Int
)
