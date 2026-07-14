# Sistema de Apostas de Cavalos

Aplicação desktop (NativePHP) / Laravel para controle de corridas, apostas, apostadores e distribuição de prêmios.

## Requisitos

- PHP **8.3+**
- [Composer](https://getcomposer.org/)
- Node.js **22+** e npm
- Extensões PHP: `mbstring`, `pdo_sqlite`, `openssl`, `fileinfo`, `zip`
- (Opcional, para app desktop) NativePHP Desktop — instalado via Composer

## Como executar o sistema

### 1. Instalar dependências

```bash
composer install
npm install
```

### 2. Configurar ambiente

```bash
cp .env.example .env
php artisan key:generate
```

Confira no `.env`:

| Variável | Descrição |
|----------|-----------|
| `DB_CONNECTION=sqlite` | Banco padrão do projeto |
| `DB_DATABASE=database/nativephp.sqlite` | Caminho do SQLite |

Crie o arquivo do banco se ainda não existir:

```bash
touch database/nativephp.sqlite
php artisan migrate
```

### 3. Rodar em desenvolvimento

**App desktop (recomendado):**

```bash
composer native:dev
```

Isso sobe o NativePHP (`php artisan native:run`) e o Vite em paralelo.

**Ou só pelo navegador:**

```bash
composer dev
```

Acesse `http://localhost:8000`.

### Setup rápido (alternativa)

```bash
composer setup
```

Executa install, `.env`, key, migrate e build do frontend de uma vez.

---

## Gerar uma versão nova

A cada release, a versão precisa subir. O updater compara essa versão com a instalada nos clientes.

### 1. Incrementar a versão

Altere nos dois lugares (mantenha o mesmo valor):

- `.env` → `NATIVEPHP_APP_VERSION=1.0.6`
- `config/nativephp.php` → fallback `'version' => env('NATIVEPHP_APP_VERSION', '1.0.6')`

### 2. Garantir configuração do updater

No `.env` (já espelhado no `.env.example`):

```env
NATIVEPHP_UPDATER_ENABLED=true
NATIVEPHP_UPDATER_PROVIDER=github
GITHUB_OWNER=wesleyvieira12
GITHUB_REPO=apostas_cavalos
GITHUB_PRIVATE=false
GITHUB_V_PREFIXED_TAG_NAME=true
GITHUB_RELEASE_TYPE=release
GITHUB_TOKEN=seu_token_aqui
```

O `GITHUB_TOKEN` precisa de permissão para criar/atualizar Releases no repositório. Ele **não** vai no pacote final do app (é removido no build).

### 3. Build dos assets

```bash
npm run build
```

### 4. Publicar a versão (obrigatório para atualizar clientes)

`native:build` só gera o instalável localmente.  
Para a atualização automática chegar aos usuários, use **`native:publish`**:

```bash
# Windows
php artisan native:publish win

# macOS / Linux (quando aplicável)
php artisan native:publish mac
php artisan native:publish linux
```

Isso faz o build **e** envia os artefatos para o GitHub Releases (tag `v1.0.6`, por exemplo).

### 5. Via CI (GitHub Actions)

Ao dar push na branch `master` (ou disparar o workflow manualmente), o job `.github/workflows/nativephp-windows.yml`:

1. Lê a versão do `config/nativephp.php`
2. Configura o updater com GitHub
3. Roda `php artisan native:publish win`
4. Publica a release e sobe o artifact do build

### Checklist rápido de release

1. [ ] Subir `NATIVEPHP_APP_VERSION` (`.env` + `config/nativephp.php`)
2. [ ] `GITHUB_TOKEN` válido no `.env` (ou secrets no Actions)
3. [ ] `npm run build`
4. [ ] `php artisan native:publish win` **ou** push em `master`
5. [ ] Conferir a release no GitHub

> **Importante:** só `native:build` não publica update. Clientes com o app instalado só atualizam se a release existir no GitHub com versão maior que a instalada.
