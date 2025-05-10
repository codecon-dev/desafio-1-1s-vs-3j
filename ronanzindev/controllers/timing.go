package controllers

import (
	"desafio-senior-vs-junior/models"
	"net/http"
	"time"

	"github.com/gin-gonic/gin"
)

func WithTiming(handler func(c *gin.Context) (any, error), status int) gin.HandlerFunc {
	return func(ctx *gin.Context) {
		start := time.Now()
		result, err := handler(ctx)

		elapsed := time.Since(start).Seconds()
		timeStamps := start.Format(time.RFC3339)
		if err != nil {
			errResponse := models.ResponseError{
				Status:          http.StatusBadRequest,
				Timestamp:       timeStamps,
				ExecutionTimeMs: elapsed * 1000,
			}
			ctx.JSON(http.StatusBadRequest, errResponse)
		}
		response := models.ApiResponse[any]{
			Status:          status,
			Timestamp:       timeStamps,
			ExecutionTimeMs: elapsed * 1000,
			Data:            result,
		}
		ctx.JSON(status, response)
	}
}
