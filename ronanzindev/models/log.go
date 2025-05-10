package models

import (
	"encoding/json"
	"time"
)

type Log struct {
	Data time.Time `json:"data"`
	Acao string    `json:"acao"`
}

func (l *Log) UnmarshalJSON(b []byte) error {
	type alias Log
	aux := &struct {
		Data string `json:"data"`
		Acao string `json:"acao"`
	}{}
	if err := json.Unmarshal(b, &aux); err != nil {
		return err
	}
	parsedTime, err := time.Parse("2006-01-02", aux.Data)
	if err != nil {
		return err
	}
	l.Data = parsedTime
	l.Acao = aux.Acao
	return nil
}
