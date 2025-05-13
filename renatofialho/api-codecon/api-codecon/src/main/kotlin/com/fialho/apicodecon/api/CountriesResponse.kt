package com.fialho.apicodecon.api

import com.fialho.apicodecon.domain.User
import java.time.OffsetDateTime

data class CountriesResponse(
    val countries: List<CountryResponse>,
    val timestamp: OffsetDateTime,
    val executionTimeMs: Long
)
