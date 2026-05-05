# Habitly - Gerenciador de Hábitos

**Habitly** é um gerenciador de hábitos pessoais focado em produtividade e simplicidade. Acompanhe seus hábitos em até 10 rotinas diárias com um sistema visual de marcação semelhante aos gráficos de contribuição do GitHub, acumule "streaks" (dias consecutivos) e mantenha-se motivado.

## Objetivo do Projeto

O objetivo principal do Habitly é fornecer uma ferramenta limpa, atrativa e fácil de usar para acompanhar sua rotina diária e atividades. Ele ajuda você a construir novos hábitos calculando ativamente quantos dias consecutivos você alcançou uma meta, e apresentando os dados com um gráfico histórico e visualização em calendário, tornando a consistência visivelmente mais encorajadora.

## Funcionalidades

- **Rastreamento de Hábitos:** Crie e gerencie até 10 hábitos de forma direta para transformar em rotina.
- **Gráfico Histórico de Contribuição:** Visualize seu progresso diário ao longo de diferentes anos de forma intuitiva, em um grid similar aos commits do GitHub.
- **Cálculo de "Streaks" (Sequências Ininterruptas):** Mantenha sua motivação em dia acompanhando as sequências de dias concluídos em cada hábito.
- **Calendário Interativo:** Visualize e marque datas com um calendário integrado.
- **Autenticação Flexível:** Login via e-mail/senha ou utilizando sua conta do Google.
- **Modos Escuro e Claro Integrados:** Alterne entre temas conforme sua preferência.

## Tecnologias Utilizadas

- **Backend:** PHP 8.4 e [Laravel 13](https://laravel.com/)
- **Frontend:** [Tailwind CSS v4](https://tailwindcss.com/) com [Vite](https://vitejs.dev/) e Blade Templates (com JavaScript Vanilla para interatividade)
- **Banco de Dados:** PostgreSQL (via Docker)
- **Autenticação:** Laravel Socialite (Login com Google)
- **Containerização:** Docker e Docker Compose

## Como Rodar o Projeto Localmente

O projeto roda inteiramente via **Docker**, eliminando a necessidade de instalar PHP, Node.js ou PostgreSQL manualmente na sua máquina.

### Pré-requisitos

- [Docker](https://www.docker.com/products/docker-desktop/) instalado e rodando
- [Docker Compose](https://docs.docker.com/compose/) (já vem incluso no Docker Desktop)

### Passo a Passo

1. **Clone o repositório:**
   ```bash
   git clone https://github.com/LuizBMJ/Projeto-Gerenciador-de-Habitos.git Project-Habit-Tracker
   cd Project-Habit-Tracker
   ```

2. **Suba os containers:**
   ```bash
   docker-compose up --build
   ```

   Esse comando irá:
   - Construir a imagem da aplicação com PHP 8.4 e Node.js 20
   - Subir o banco de dados PostgreSQL
   - Aguardar o banco ficar disponível
   - Executar as migrations automaticamente
   - Gerar a `APP_KEY` caso não exista
   - Iniciar o servidor Laravel (porta `10000`) e o Vite (porta `5173`)

3. **Acesse a aplicação:**
   Abra seu navegador em `http://localhost:10000`

### Comandos Úteis

```bash
# Parar os containers
docker-compose down

# Parar e remover os volumes (reseta o banco de dados)
docker-compose down -v

# Rodar em segundo plano
docker-compose up -d

# Ver logs da aplicação
docker-compose logs -f app

# Acessar o terminal do container
docker-compose exec app bash
```

### (Opcional) Configurando o Google Auth

Caso queira habilitar o login via Google, adicione as credenciais no arquivo `.env` na raiz do projeto:

```env
GOOGLE_CLIENT_ID="sua_client_id_aqui"
GOOGLE_CLIENT_SECRET="seu_client_secret_aqui"
GOOGLE_REDIRECT_URI="http://localhost:10000/auth/google/callback"
```

> **Nota:** Este passo **não** é obrigatório. Você pode usar o formulário normal de registro/login sem configurar o Google.

## Estrutura de Arquivos Docker

```
dockerfile/
├── dev/
│   └── Dockerfile.dev    # Ambiente local com hot-reload (Vite)
└── prod/
    └── Dockerfile.prod   # Produção com assets pré-compilados (multi-stage build)

docker-entrypoint.sh      # Script de inicialização do ambiente dev
docker-compose.yml        # Orquestração local (app + PostgreSQL)
.dockerignore             # Arquivos excluídos do contexto Docker
```
