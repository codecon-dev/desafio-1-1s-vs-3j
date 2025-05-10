package main

import (
	"desafio-senior-vs-junior/controllers"
	"desafio-senior-vs-junior/repository"
	"desafio-senior-vs-junior/services"

	"github.com/gin-gonic/gin"
)

func main() {
	repo := repository.NewUsuarioRepository()
	service := services.NewAnaliseService(repo)
	controller := controllers.NewUsuarioController(service, repo)
	r := gin.Default()
	r.POST("/users", controller.Carregar())
	r.GET("/superusers", controller.SuperUsuarios())
	r.GET("/top-countries", controller.TopPaises())
	r.GET("/team-insights", controller.InsightsEquipes())
	r.GET("/active-users-per-day", controller.Logins())
	r.GET("/evaluation", controllers.AutoAvaliacao())
	if err := r.Run(":8000"); err != nil {
		panic(err)
	}

}
