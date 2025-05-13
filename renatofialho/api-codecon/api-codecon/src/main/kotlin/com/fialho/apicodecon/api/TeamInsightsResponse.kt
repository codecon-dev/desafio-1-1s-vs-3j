package com.fialho.apicodecon.api

import com.fialho.apicodecon.domain.User
import java.time.OffsetDateTime

data class TeamInsightsResponse(
    val teams: List<TeamInsightResponse>,
    val timestamp: OffsetDateTime,
    val executionTimeMs: Long
)
