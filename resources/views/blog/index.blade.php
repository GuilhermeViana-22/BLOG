@extends('layouts.layout')

@section('content')
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Layout</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
    <div class="row">

        <!-- Coluna Lateral -->
        <section class="col-md-3 p-4 d-flex flex-column" style="min-height: 100vh;">

            <article class="bg-white p-3 mb-3" style="border-radius: 8px;">
                <!-- Conteúdo do Card 1 -->
                <ul>
                    <li class="icon_article_li"><span class="emoji">🏠</span>Home</li>
                    <li class="icon_article_li"><span class="emoji">💻</span>DEV++</li>
                    <li class="icon_article_li"><span class="emoji">📚</span>Reading List</li>
                    <li class="icon_article_li"><span class="emoji">🎙️</span>Podcasts</li>
                    <li class="icon_article_li"><span class="emoji">🎥</span>Videos</li>
                    <li class="icon_article_li"><span class="emoji">🏷️</span>Tags</li>
                    <li class="icon_article_li"><span class="emoji">🆘</span>DEV Help</li>
                    <li class="icon_article_li"><span class="emoji">🛒</span>Forem Shop</li>
                    <li class="icon_article_li"><span class="emoji">📢</span>Advertise on DEV</li>
                    <li class="icon_article_li"><span class="emoji">🏆</span>DEV Challenges</li>
                    <li class="icon_article_li"><span class="emoji">✨</span>DEV Showcase</li>
                    <li class="icon_article_li"><span class="emoji">ℹ️</span>About</li>
                    <li class="icon_article_li"><span class="emoji">✉️</span>Contact</li>
                    <li class="icon_article_li"><span class="emoji">📚</span>Guides</li>
                    <li class="icon_article_li"><span class="emoji">⚙️</span>Software comparisons</li>
                </ul>
            </article>

        </section>

        <!-- Coluna Principal 1 -->
        <section class="col-md-6">
            <div class="botoes">
                <button type="button" class="btn btn-light">Rcentes</button>
                <button type="button" class="btn btn-light">Laravel</button>
                <button type="button" class="btn btn-light">Node</button>
                <button type="button" class="btn btn-light">Ùltima</button>
                <button type="button" class="btn btn-primary novo-post">Novo Post</button>
            </div>
            <article>
                <article class="bg-white p-3 mb-3" style="border-radius: 8px;">
                    <div class="col-md-6 text-align-left">
                        <div class="custom-card">
                            <div class="custom-card-header">
                                Featured
                            </div>
                            <div class="custom-card-body">
                                <h5 class="card-title">Special title treatment</h5>
                                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                            </div>
                            <div class="custom-card-footer">
                                <button type="button" title="Estrela">⭐</button>
                                <button type="button" title="Like">💎</button>
                                <button type="button" title="Coração">❤️</button>
                                <button type="button" title="Raiva">😡</button>
                                <button type="button" title="Nojo">😖</button>
                                <button type="button" title="Compartilhar">🔄</button>
                            </div>
                        </div>
                    </div>
                </article>
                <article class="bg-white p-3 mb-3" style="border-radius: 8px;">
                    <!-- Conteúdo do Card 2 -->
                    <h3>Card 2</h3>
                    <p>Conteúdo do card 2.</p>
                </article>
                <article class="bg-white p-3" style="border-radius: 8px;">
                    <!-- Conteúdo do Card 3 -->
                    <h3>Card 3</h3>
                    <p>Conteúdo do card 3.</p>
                </article>
            </article>
        </section>

        <!-- Coluna Principal 2 -->
        <section class="col-md-3 sidebar p-3">
            <div class="card">
                <div class="card-header">
                    Atividades
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item p-4">An item</li>
                        <li class="list-group-item p-4">A second item</li>
                        <li class="list-group-item p-4">A third item</li>
                        <li class="list-group-item p-4">A fourth item</li>
                        <li class="list-group-item p-4">And a fifth one</li>
                    </ul>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

@endsection
