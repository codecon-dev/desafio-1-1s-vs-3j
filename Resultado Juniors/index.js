const express = require("express");
const multer = require("multer");
const bodyparser = require("body-parser");
const fs = require('fs');

const getDateNow = require("./src/utils/getDateNow");
const { error } = require("console");

const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, "./");
  },
  filename: (req, file, cb) => {
    cb(null, getDateNow() + "-" + file.originalname);
  },
});

let data;

const upload = multer({ storage });

const app = express();
const port = 3000;

app.use(bodyparser.urlencoded({ extended: true }));
app.use(bodyparser.json());

app.get("/", (req, res) => {
  res.send("Ta funfando");
});

app.post("/users", upload.single("arquivo"), (req, res) => {
  let inicio = Date.now();

  if (!req.file) {
    res.status(400).json({ error: "Sem arquivo upado" });
  }
  try {
    console.log(req.file);
    fs.readFile(req.file.path, 'utf8', (err, fileData) => {
        if(err) {
            console.error(err);
            return            
        }
        data = JSON.parse(fileData);
    });
    let fim = Date.now();
    res.status(201).json({
      message: "Arquivo salvo com sucesso",
      timeEnlapse: (fim - inicio)/1000,
      details: req.file,
    });
  } catch (error) {
    console.log(`${getDateNow()} | Erro ao salvar arquivo `, error);
    res.status(500).json({
      message: "Não foi possivel salvar o arquivo",
      error: error.message,
    });
  }
});

app.get('/superusers', (req, res) => {
    try {
        const inicio = Date.now();
    console.log(`${inicio} | Acessado rota superusers`);

    const filtredData = data.filter(user => user.score >= 900 && user.ativo === true)

    const fim = Date.now();
    res.status(200).json({total: filteredData.length, timeEnlapse: (fim - inicio)/1000 , user_list: filteredData});
    } catch (erro) {
        console.log(`${getDateNow()} | Erro ao filtrar superusers `, error);
    res.status(500).json({
      message: "Não foi possivel filtrar superusers",
      error: error.message,
    });
    }
})

// app.get('/top-coutries'(res) => {
//     // res.status(200).json({ conta})
// })

app.listen(port, () => {
  console.log(`${getDateNow()} | API rodando na porta ${port}`);
});
