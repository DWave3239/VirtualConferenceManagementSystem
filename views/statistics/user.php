<div class="col statisticHeader">
    <h5><a href="{$_base_}statistics/index"><i class="fas fa-chevron-circle-left"></i></a> Statistics of User <em>{$full_name}</em></h5>
</div>
<div class="statisticScreen row full-screen">
    <div class="col-3">
        <table class="table table-sm">
            <thead>
                <tr>
                    <th><i class="fas fa-hourglass-half"></i> Time line</th>
                </tr>
            </thead>
            <tbody>
            {loop="$overall"}
                <tr>
                    <td><small>{$value.timestamp}</small> - {$eventIcons[$value.eventName]} <a href="{$_base_}statistics/room/{$value.roomName}">{$value.roomName}</a></td>
                </tr>
            {/loop}
            </tbody>
        </table>
    </div>
    <div class="col-9 flex-container">
    {loop="$rooms"}
        <div class="statistics card">
            <div class="card-body">
                <h5 class="card-title">{if="$key1 != '0'"}<a href="{$_base_}statistics/room/{$key}">{$key}</a>{else}Time spent{/if}</h5>
                <p class="card-text">
                    Summed time: {$roomsSums[$key1]['span_string']}<br>
                    <small>
                    {if="$key1 != '0'"}
                    {loop="$value1"}{$value2.span_string}{if="count($value1)-1 > $counter2"} + {/if}{/loop}
                    <div class="card">
                        <div class="card-header statisticsDetail" id="roomDetails{$key1}" data-toggle="collapse" data-target="#roomDetails{$key1}Card" aria-expanded="true" aria-controls="roomDetails{$key1}Card">
                            Details
                        </div>
                        <div id="roomDetails{$key1}Card" class="collapse" aria-labelledby="roomDetails{$key1}" data-parent="#roomDetails{$key1}">
                            <div class="card-body">
                                {loop="$roomsDetails[$key1]"}
                                {$value2.timestamp} - {function="substr($value2.eventName, strpos($value2.eventName, '_')+1)"}<br>
                                {/loop}
                            </div>
                        </div>
                    </div>
                    {else}
                    <br />
                    {/if}
                    </small>
                </p>
            </div>
        </div>
    {/loop}
    </div>
</div>