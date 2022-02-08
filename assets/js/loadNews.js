var newsAlreadyLoaded = [];
function loadNews(newsContainerId){
    if(newsAlreadyLoaded[newsContainerId]){
        return;
    }else{
        newsAlreadyLoaded[newsContainerId] = 0;
        loadNextNews(newsContainerId);
        setInterval(function() { if ( new Date().getSeconds() === 0 ) loadNextNews(newsContainerId); }, 1000);
    }
}

function loadNextNews(newsContainerId){
    $.ajax({
        url: 'index.php?main/news/'+newsAlreadyLoaded[newsContainerId],
        dataType: 'json',
        success: function(result){
            if(result){
                if($('#'+newsContainerId)){
                    result.forEach(row => addNewsRowAsCard(newsContainerId, row));
                }
            }
        }
    });
}

function addNewsRowAsCard(newsContainerId, row){
    var card = $('<div class="card"><div class="card-header" id="news'+row.newsId+'Header" data-toggle="collapse" data-target="#news'+row.newsId+'Card" aria-expanded="true" aria-controls="news'+row.newsId+'Card">'+row.title+'</div><div id="news'+row.newsId+'Card" class="collapse show" aria-labelledby="news'+row.newsId+'Header" data-parent="#news'+row.newsId+'Header"><div class="card-body">'+row.description+'</div></div></div>')
    $('#'+newsContainerId).find('.modal-body').prepend(card);
    newsAlreadyLoaded[newsContainerId] = row.newsId;
}