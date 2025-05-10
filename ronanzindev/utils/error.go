package utils

import (
	"time"

	"github.com/gin-gonic/gin"
)

type Error struct {
	Code    int    `json:"code"`
	Message string `json:"message"`
	Time    string `json:"time"`
}

func ResponseWithError(c *gin.Context, code int, err string) {
	var error Error
	error.Code = code
	error.Message = err
	error.Time = time.Now().Format(time.RFC3339)
	if code == 404 || code == 400 {
		c.JSON(code, gin.H{
			"message": error.Message,
			"time":    error.Time,
			"code":    error.Code,
			"status":  "error",
		})
		return
	}
	if code == 500 {
		error.Message = "internal server error"
	}
	c.JSON(code, gin.H{
		"message": error.Message,
		"time":    error.Time,
		"code":    error.Code,
		"status":  "error",
	})
}
