{loop="$news"}
<div class="card">
    <div class="card-header" id="news{$value.newsId}Header" data-toggle="collapse" data-target="#news{$value.newsId}Card" aria-expanded="true" aria-controls="news{$value.newsId}Card">
        {$value.title} - {$value.displayAfter}
    </div>
    <div id="news{$value.newsId}Card" class="collapse show" aria-labelledby="news{$value.newsId}Header" data-parent="#news{$value.newsId}Header">
        <div class="card-body">
            {$value.description}
        </div>
    </div>
</div>
{/loop}

<script src="assets/js/loadNews.js"></script>