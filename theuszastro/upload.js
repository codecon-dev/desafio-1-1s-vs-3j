const busboy = require('busboy');

const middleware = parse => {
	return async (req, res) => {
		const { 'content-type': contentType } = req.headers;

		if (contentType.includes('multipart/form-data')) {
			const instance = busboy({ headers: req.headers, limits: { files: 1 } });
			const buffers = [];

			instance.on('file', async (_, stream, info) => {
				for await (const chunk of stream) {
					buffers.push(chunk);
				}
			});

			instance.on('close', async () => {
				const file = JSON.parse(Buffer.concat(buffers));

				parse(file);

				return res.status(200).json({ message: 'Arquivo recebido com sucesso', user_count: file.length });
			});

			req.pipe(instance);

			return;
		}

		return res.status(401).json({ error: 'Arquivo não enviado..' });
	};
};

module.exports = {
	middleware,
};
