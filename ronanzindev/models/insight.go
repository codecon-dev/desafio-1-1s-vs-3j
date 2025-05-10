package models

type Insight struct {
	Equipe             string  `json:"equipe"`
	TotalMembros       int     `json:"totalMembros"`
	Lideres            int     `json:"lideres"`
	ProjetosConcluidos int     `json:"projetosConcluidos"`
	PorcentualAtivos   float64 `json:"portentualAtivos"`
}
