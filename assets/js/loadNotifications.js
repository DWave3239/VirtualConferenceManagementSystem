var notificationsAlreadyLoaded = [];
function loadNotifications(notificationsContainerId){
    if(notificationsAlreadyLoaded[notificationsContainerId]){
        return;
    }else{
        notificationsAlreadyLoaded[notificationsContainerId] = 0;
        loadNextNotifications(notificationsContainerId);
        setInterval(function() { if ( new Date().getSeconds() === 0 ) loadNextNotifications(notificationsContainerId); }, 1000);
    }
}

function loadNextNotifications(notificationsContainerId){
    $.ajax({
        url: 'index.php?main/notifications/'+notificationsAlreadyLoaded[notificationsContainerId],
        dataType: 'json',
        success: function(result){
            if(result){
                if($('#'+notificationsContainerId)){
                    result.forEach(row => addNotificationRowAsCard(notificationsContainerId, row));
                }
            }
        }
    });
}

function addNotificationRowAsCard(notificationsContainerId, row){
    console.log(row);
    var card = $('<div class="card"><div class="card-header" id="notifications'+row.notificationId+'Header" data-toggle="collapse" data-target="#notifications'+row.notificationId+'Card" aria-expanded="true" aria-controls="notifications'+row.notificationId+'Card">'+row.title+'</div><div id="notifications'+row.notificationId+'Card" class="collapse show" aria-labelledby="notifications'+row.notificationId+'Header" data-parent="#notifications'+row.notificationId+'Header"><div class="card-body">'+row.description+'</div></div></div>')
    $('#'+notificationsContainerId).find('.modal-body').prepend(card);
    notificationsAlreadyLoaded[notificationsContainerId] = row.notificationId;
}