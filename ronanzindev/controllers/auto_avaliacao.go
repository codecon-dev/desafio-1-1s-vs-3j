package controllers

import (
	"encoding/json"
	"fmt"
	"net/http"
	"time"

	"github.com/gin-gonic/gin"
)

type response struct {
	Status          int     `json:"status"`
	ExecutionTimeMs float64 `json:"execution_time_ms"`
	TimeStamp       string  `json:"timestamp"`
	ValidResponse   bool    `json:"valid_response"`
}

// GET /evaluation
// Ele deve executar uma autoavaliação dos principais endpoints da API e retornar um relatório de pontuação.

// A avaliação deve testar:

// Se o status retornado é 200
// O tempo em milisegundos de resposta
// Se o retorno é um JSON válido
func AutoAvaliacao() gin.HandlerFunc {
	return WithTiming(func(c *gin.Context) (any, error) {
		endpoints := []string{"superusers", "top-countries", "team-insights", "active-users-per-day"}
		results := make(map[string]response, 0)
		for _, endpoint := range endpoints {
			res := response{}
			start := time.Now()
			httpResponse, err := http.Get(fmt.Sprintf("http://localhost:8000/%s", endpoint))
			if err != nil {
				return nil, err
			}
			defer httpResponse.Body.Close()
			res.Status = httpResponse.StatusCode
			var data any
			err = json.NewDecoder(httpResponse.Body).Decode(&data)
			if err != nil {
				res.ValidResponse = false
			} else {
				res.ValidResponse = true
			}
			elapsed := time.Since(start).Seconds() * 1000
			res.ExecutionTimeMs = elapsed
			res.TimeStamp = start.Format(time.RFC3339)
			results[endpoint] = res
		}
		return results, nil
	}, 200)
}
