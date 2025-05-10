package models

type Equipe struct {
	Nome     string    `json:"nome"`
	Lide     bool      `json:"lider"`
	Projetos []Projeto `json:"projetos"`
}
