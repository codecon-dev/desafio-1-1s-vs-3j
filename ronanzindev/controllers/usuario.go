package controllers

import (
	"desafio-senior-vs-junior/models"
	"desafio-senior-vs-junior/repository"
	"desafio-senior-vs-junior/services"
	"net/http"
	"strconv"

	"github.com/gin-gonic/gin"
)

type UsuarioController struct {
	repo    *repository.UsuarioRepository
	service *services.AnaliseService
}

func NewUsuarioController(service *services.AnaliseService, repo *repository.UsuarioRepository) *UsuarioController {
	return &UsuarioController{service: service, repo: repo}
}

func (u *UsuarioController) Carregar() gin.HandlerFunc {
	return WithTiming(func(c *gin.Context) (any, error) {
		var body []models.User
		if err := c.BindJSON(&body); err != nil {
			return nil, err
		}
		u.repo.SaveAll(body)
		return gin.H{"mensagem": "Arquivo recebido com sucesso", "user_count": len(u.repo.FindAll().Collect())}, nil
	}, http.StatusOK)

}

func (u *UsuarioController) SuperUsuarios() gin.HandlerFunc {
	return WithTiming(func(c *gin.Context) (any, error) {
		superUsuarios := u.service.SuperUsuarios()
		return superUsuarios, nil
	}, http.StatusOK)

}

func (u *UsuarioController) TopPaises() gin.HandlerFunc {
	return WithTiming(func(c *gin.Context) (any, error) {
		topPaises := u.service.TopPaises()
		return topPaises, nil
	}, http.StatusOK)

}

func (u *UsuarioController) InsightsEquipes() gin.HandlerFunc {
	return WithTiming(func(c *gin.Context) (any, error) {
		insights := u.service.InsightEquipes()
		return insights, nil
	}, http.StatusOK)
}

func (u *UsuarioController) Logins() gin.HandlerFunc {
	return WithTiming(func(c *gin.Context) (any, error) {
		minQuery := c.Query("min")
		min, _ := strconv.Atoi(minQuery)
		logins := u.service.LoginPorDia(min)
		return logins, nil
	}, http.StatusOK)
}
