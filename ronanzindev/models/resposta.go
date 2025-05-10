package models

type ApiResponse[T any] struct {
	Status          int     `json:"status"`
	Timestamp       string  `json:"timestamps"`
	ExecutionTimeMs float64 `json:"execution_time_ms"`
	Data            T       `json:"data"`
}

type ResponseError struct {
	Status          int     `json:"status"`
	Timestamp       string  `json:"timestamps"`
	ExecutionTimeMs float64 `json:"execution_time_ms"`
	Error           string  `json:"error"`
}
