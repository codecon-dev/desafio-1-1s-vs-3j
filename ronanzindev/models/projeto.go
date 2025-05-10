package models

type Projeto struct {
	Nome      string `json:"nome"`
	Concluido bool   `json:"concluido"`
}
