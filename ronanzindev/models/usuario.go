package models

type User struct {
	ID     string `json:"id"`
	Nome   string `json:"nome"`
	Idade  int    `json:"idade"`
	Score  int    `json:"score"`
	Ativo  bool   `json:"ativo"`
	Pais   string `json:"pais"`
	Equipe Equipe `json:"equipe"`
	Logs   []Log  `json:"logs"`
}
