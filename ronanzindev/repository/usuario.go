package repository

import (
	"desafio-senior-vs-junior/models"

	"github.com/ronanzindev/compress/stream"
)

type UsuarioRepository struct {
	db map[string]models.User
}

// POST /users
// Recebe e armazena os usuários na memória. Pode simular um banco de dados em memória.
func NewUsuarioRepository() *UsuarioRepository {
	return &UsuarioRepository{
		db: make(map[string]models.User),
	}
}

func (u *UsuarioRepository) SaveAll(users []models.User) {
	for _, user := range users {
		u.db[user.ID] = user
	}
}

func (u *UsuarioRepository) FindAll() *stream.Stream[models.User] {
	users := make([]models.User, 0)
	for _, user := range u.db {
		users = append(users, user)
	}
	return stream.NewStream(users)
}
