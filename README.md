# Linktree Privado

Página pública de links em PHP 8.3, com o visual Essencial e perfis armazenados em JSON. O projeto funciona sem banco de dados e sem painel: as alterações são feitas no arquivo de perfis e publicadas pelo Git.

A paleta acompanha o currículo de Henry: azul-marinho #1A2B4A, azul #2E6DA4 e cinza #4A5568 sobre fundos claros, com adaptação para o tema escuro. Botões, ícone da página e cor da interface do navegador seguem essa paleta.

O perfil de Henry Duarte Marth está em /henry-marth, com o subtítulo Desenvolvedor Fullstack / Automações, links para GitHub e LinkedIn, o currículo em PDF e a foto fornecida.

## Onde editar

- data/profiles.json: nomes, subtítulos, fotos e links. Fica fora da pasta pública.
- public/files/curriculos/henry-duarte-marth.pdf: currículo de Henry, copiado do arquivo fornecido.
- public/assets/profiles/henry-marth.jpg: foto de Henry, incluída no deploy.
- public/assets/styles.css: estilos da página.

Cada chave do objeto JSON é o slug da pessoa, por exemplo henry-marth. Para adicionar o perfil de outra pessoa, adicione outra chave com a mesma estrutura. Os campos são:

| Campo | Uso |
| --- | --- |
| name | Nome exibido no perfil |
| bio | Subtítulo ou apresentação curta |
| avatar_url | Caminho em /assets/profiles/ ou URL HTTP/HTTPS da foto; null mostra as iniciais |
| is_active | false oculta o perfil; o padrão é true |
| links | Lista de cards na ordem desejada |

Cada card tem kind, title, url e description opcional. Os tipos disponíveis são github, linkedin, resume e link. Use is_active: false em um card para ocultá-lo. A ordem do array define a ordem na página. O arquivo deve ser JSON válido, sem comentários ou vírgula após o último item.

Os dados ficam versionados no repositório. O deploy inclui o JSON e os PDFs na imagem Docker; não é necessário criar banco ou configurar volume para este fluxo de edição pelo Git.

## Rotas

| Rota | Resposta |
| --- | --- |
| GET /henry-marth | Página do perfil de Henry |
| GET /api/profiles/henry-marth | Mesmo perfil em JSON |
| GET /curriculo/henry-marth | Download do currículo em PDF |
| GET /qr/henry-marth | QR code SVG para abrir o perfil |
| GET /healthz | Health check do container |

As mesmas rotas funcionam para cada novo slug cadastrado. Perfis inexistentes ou inativos retornam 404. A página inicial apenas orienta o visitante a usar o endereço completo do perfil. O rodapé de cada perfil permite abrir e baixar seu QR code.

## Currículos em PDF

Coloque os arquivos em public/files/curriculos/ e cadastre o caminho no JSON, por exemplo /files/curriculos/henry-duarte-marth.pdf com kind igual a resume. Use nomes de arquivo sem espaços ou acentos. Cadastre um currículo ativo por perfil.

O botão passa pela rota /curriculo/{slug} e envia o arquivo como anexo com nome curriculo-{slug}.pdf. Arquivos ausentes retornam uma página 404. Para atualizar o currículo, substitua o PDF e publique novamente.

Também é possível cadastrar uma URL HTTP/HTTPS de PDF. Nesse caso, a rota redireciona para o servidor externo, que controla se o PDF será aberto ou baixado. Para garantir o download, use um arquivo local.

## Deploy no EasyPanel

Crie um serviço a partir do repositório e escolha o Dockerfile. Ele instala as versões do composer.lock, configura Apache/PHP e expõe somente a pasta public. data/profiles.json e os arquivos de código ficam fora da raiz pública; a API entrega apenas os perfis públicos normalizados.

| Variável | Exemplo | Uso |
| --- | --- | --- |
| APP_URL | https://links.megamaxtech.com.br | URL canônica do serviço |
| PUBLIC_BASE_URL | https://links.megamaxtech.com.br | Base dos endereços codificados nos QR codes |

Configure a porta interna 80, associe o domínio e habilite HTTPS. O perfil estará em https://seu-dominio/henry-marth. Nenhuma variável DB_* é utilizada.

As páginas e a API permitem cache público por 60 segundos; QR codes por 24 horas. Alterações podem demorar esse tempo para aparecer em caches. A API JSON permite CORS *.

## Desenvolvimento local

Com PHP 8.3 e Composer, instale as dependências e inicie o servidor a partir da pasta do projeto:

~~~sh
composer install
php -S 127.0.0.1:8080 -t public
~~~

Abra http://127.0.0.1:8080/henry-marth. Para o QR code usar o endereço local, defina APP_URL e PUBLIC_BASE_URL como http://127.0.0.1:8080 no ambiente antes de iniciar o PHP.

Também é possível executar com Docker. Copie .env.example para .env e ajuste as duas URLs:

~~~sh
docker build -t linktree-privado .
docker run --rm -p 8080:80 --env-file .env linktree-privado
~~~

O PHP recebe as variáveis do ambiente; não lê .env por conta própria. O layout se adapta ao celular e ao tema claro/escuro do dispositivo. CSS e ícones são locais e a página não depende de JavaScript. Os ícones são do Lucide; a licença está em public/assets/lucide-LICENSE.txt.
