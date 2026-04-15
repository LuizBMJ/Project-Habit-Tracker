# Habitly - Gerenciador de Hábitos

**Habitly** é um gerenciador de hábitos pessoais focado em produtividade e simplicidade. Acompanhe seus hábitos em até 10 rotinas diárias com um sistema visual de marcação semelhante aos gráficos de contribuição do GitHub, acumule "streaks" (dias consecutivos) e mantenha-se motivado.

## Objetivo do Projeto

O objetivo principal do Habitly é fornecer uma ferramenta limpa, atrativa e fácil de usar para acompanhar sua rotina diária e atividades. Ele ajuda você a construir novos hábitos calculando ativamente quantos dias consecutivos você alcançou uma meta, e apresentando os dados com um gráfico histórico e visualização em calendário, tornando a consistência visivelmente mais encorajadora.

## Funcionalidades

- **Rastreamento de Hábitos:** Crie e gerencie até 10 hábitos de forma direta para transformar em rotina.
- **Gráfico Histórico de Contribuição:** Visualize seu progresso diário ao longo de diferentes anos de forma intuitiva, em um grid similar aos commits do GitHub.
- **Cálculo de "Streaks" (Sequências Ininterruptas):** Mantém sua motivação la em cima apresentando as sequências de seus dias em cada hábito.
- **Calendário Interativo:** Visualize e altere as datas com um calendário (Só não tente colocar que fez algo em um dia que ainda não chegou, ok?).
- **Autenticação Flexível:** Login através de um login (obviamente) ou utilizando sua conta do Google.
- **Modos Escuro e Claro Integrados:** Acho que é auto explicativo.

## Tecnologias Utilizadas

Este projeto foi construído em uma stack moderna, veloz e robusta de Backend e Frontend:

- **Backend:** PHP 8.4 e o Framework [Laravel 13](https://laravel.com/).
- **Frontend:** [Tailwind CSS v4](https://tailwindcss.com/) acoplado ao Vite, construindo telas pelas Blade Templates (com partes customizadas orientadas com JavaScript Vanilla para dinâmica de dados).
- **Banco de Dados:** Versátil, podendo utilizar Servidor Local via XAMPP (MySQL/MariaDB) para Desenvolvimento ou PostgreSQL (usualmente utilizado em implantação na provedora Render).
- **Autenticação:** Laravel Socialite (para Google Login).
- **Infraestrutura:** A raiz conta com um `Dockerfile`, útil para gerenciar a publicação e portabilidade da arquitetura em ambientes em nuvem.

## Como Rodar o Projeto Localmente

Apesar da disposição do repositório vir contida com um `Dockerfile`, este arquivo foi planejado em tese para rodar o comando final de compilação em serviços de cloud computing (como Render). Dessa maneira, não posso garantir o seu funcionamento para rodar puramente local. Se você utiliza o **XAMPP** para gerenciar recursos MySQL e PHP, use essas instruções para rodar nativamente:

### Pré-requisitos
- PHP 8.3 ou superior instalado e liberado em Variáveis de Ambiente.
- [Node.js](https://nodejs.org/en/) e gerenciador universal NPM para empacotamento JavaScript e CSS.
- [Composer](https://getcomposer.org/) so baixa ai.
- XAMPP (Opcional, porém amplamente recomendado para emulação em servidor Apache/MySQL no Desenvolvimento).

### Passo a passo para o ambiente de Desenvolvimento e Localhost

1. **Clone do repositório remoto:**
   ```bash
   git clone https://github.com/LuizBMJ/Projeto-Gerenciador-de-Habitos.git Project-Habit-Tracker
   cd Project-Habit-Tracker
   ```

2. **Instalação das Bibliotecas por Dependência (Vendors e Nodes):**
   ```bash
   composer install
   npm install
   ```

3. **Iniciando o Servidor de Banco de Dados de Retaguarda (XAMPP):**
   - Inicie o servio **Apache** e **MySQL** dentro do seu Painel de Controle no XAMPP ou local de escolha.
   - Abra a aba `http://localhost/phpmyadmin` no seu navegador favorito (Brave né?).
   - Entre no terminal de SQL e rode `CREATE DATABASE project_habit_tracker;` ou simplesmente crie um banco nomeado como `project_habit_tracker` direto pela interface do PHPMyAdmin.

4. **Estruturando o Arquivo de Credenciais e Variáveis Locais (`.env`):**
   - Dentro da raiz do repositório, você vai identificar um arquivo chamado `.env.example`, vamos copiar essa ele:
     ```bash
     cp .env.example .env
     ```
   - Gere imediatamente uma Chave Aleatória de Segurança e Assinatura Criptográfica para seu Projeto:
     ```bash
     php artisan key:generate
     ```
   - Abra seu novo arquivo `.env` para apontar ao seu MySQL ativo (como o foco agora é a execução manual em vez do Render apontando para `pgsql`, mude-as preferencialmente para MySQL):
     ```env
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=project_habit_tracker
     DB_USERNAME=root
     DB_PASSWORD=
     ```

5. **(Opcional) Testando e Ativando as Configurações do Google Auth**
   Caso você ou colegas pretendam utilizar o Login pelo Google, precisará gerar um ID no Google Cloud e passar suas credenciais na devida seção do sistema (Esse bagulho é chato viu):
   ```env
   GOOGLE_CLIENT_ID="sua_client_id_aqui"
   GOOGLE_CLIENT_SECRET="seu_client_secret_aqui"
   GOOGLE_REDIRECT_URI="http://127.0.0.1:8000/auth/google/callback"
   ```
   > **Aviso Importante:** Este passo **NÃO** é estritamente obrigatório. Você está livre para utilizar o formulário normal de registro/login caso não tenha intenção de testar o Log-in via Google no ambiente de testes.

6. **Refletir Estruturas da Base e Executar as Migrations:**
   Vai bota tudo no seu recém criado banco MySQL através dos "plants" de estrutura do Laravel:
   ```bash
   php artisan migrate
   ```

7. **Inicie, por final, o Servidor Integrado:**
   So confia e faz:
   
   **Pelo Script Concorrente do Composer** (Roda npm e PHP serve juntos):
   ```bash
   composer run dev
   ```

   **Alternativamente, em duas abas de terminal ou CLI independentes:**
   ```bash
   php artisan serve
   ```
   *E no terminal número 2:*
   ```bash
   npm run dev
   ```

Se tudo for perfeitamente aplicado até este momento (no meu caso ja teria feito tudo errado) basta só clicar no "link" que aparece no terminal (quase sempre `http://127.0.0.1:8000`), para iniciar o site.
