# Use a imagem oficial do Node.js
FROM node:16

# Defina o diretório de trabalho
WORKDIR /app

# Copie os arquivos package.json e package-lock.json
COPY ui/package*.json ./

# Instale todas as dependências, incluindo as de desenvolvimento
RUN npm install --include=dev

# Copie o código da aplicação para o contêiner
COPY ui ./

# Exponha a porta 8080
EXPOSE 8080

# Execute o comando para iniciar o servidor
CMD ["npm", "run", "serve"]
