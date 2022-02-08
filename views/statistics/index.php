<div class="col statisticHeader">
    <h5>Event Overview</h5>
</div>
<div class="statisticScreen row full-screen">
    <div class="col-2">
        <div class="row vh-50 align-items-start">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Rooms</th>
                    </tr>
                </thead>
                <tbody>
                    {loop="$rooms"}
                        <tr><td><a href="{$_base_}statistics/room/{$value}">{$value}</a></td></tr>
                    {/loop}
                </tbody>
            </table>
        </div>
        <div class="row vh-50 align-items-start">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Users</th>
                    </tr>
                </thead>
                <tbody>
                    {loop="$users"}
                        <tr><td><a href="{$_base_}statistics/user/{$value.userId}">{$value.full_name}</a></td></tr>
                    {/loop}
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-10">
        <table class="table table-sm table-hover">
            <thead class="stickyHeader thead-light">
                <tr>
                    <th class="stickyHeader" scope="col">Timestamp</th>
                    <th class="stickyHeader" scope="col">Event</th>
                    <th class="stickyHeader" scope="col">Description</th>
                </tr>
            </thead>
            <tbody>
                {loop="$list"}
                    <tr>
                        <td>{$value.timestamp}</td>
                        <td>{$eventIcons[$value.eventName]}</td>
                        <td>
                            {if="$value.eventName == 'ROOM_CREATED'"}
                                Room <a href="{$_base_}statistics/room/{$value.roomName}">{$value.roomName}</a> has been created.
                            {/if}
                            {if="$value.eventName == 'ROOM_DESTROYED'"}
                                Room <a href="{$_base_}statistics/room/{$value.roomName}">{$value.roomName}</a> has been destroyed.
                            {/if}
                            {if="$value.eventName == 'PARTICIPANT_JOINED'"}
                                <a href="{$_base_}statistics/user/{$value.userId}">{$value.full_name}</a> entered the room <a href="{$_base_}statistics/room/{$value.roomName}">{$value.roomName}</a>.
                            {/if}
                            {if="$value.eventName == 'PARTICIPANT_LEFT'"}
                                <a href="{$_base_}statistics/user/{$value.userId}">{$value.full_name}</a> left the room <a href="{$_base_}statistics/room/{$value.roomName}">{$value.roomName}</a>.
                            {/if}
                        </td>
                    </tr>
                {/loop}
            </tbody>
        </table>
    </div>
</div>