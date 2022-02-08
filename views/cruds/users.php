{if="$action == 'list'"}
<table class="table table-sm">
    <thead>
        <tr>
            <th>Full name</th>
            <th>Authentication Level</th>
            <th>
                <a href="{$_base_}backend/{$url}/add" class="btn btn-primary">Add</a>
            </th>
        </tr>
    </thead>
    <tbody>
    {loop="$list"}
        <tr>
            <td>{$value.full_name}</td>
            <td>
                {if="$value.auth_level == $auth_level_participant"}Participant{/if}
                {if="$value.auth_level == $auth_level_presenter"}Author{/if}
                {if="$value.auth_level == $auth_level_session_chair"}Session Chair{/if}
                {if="$value.auth_level == $auth_level_program_chair"}Program Chair{/if}
                {if="$value.auth_level == $auth_level_administrator"}Adminstrator{/if}
            </td>
            <td>
                <a href="{$_base_}backend/{$url}/edit/{$value.userId}" class="btn btn-primary">Edit</a>
                <a href="#" data-href="{$_base_}backend/{$url}/delete/{$value.userId}" class="btn btn-primary" data-toggle="modal" data-target="#crudDeletionOverlay">Delete</a>
                <a href="{$_base_}statistics/user/{$value.userId}" class="btn btn-primary"><i class="fas fa-chart-bar"></i></a>
            </td>
        </tr>
    {/loop}
    </tbody>
</table>
{/if}

{if="$action == 'edit' || $action == 'add'"}
<form method="post" action="{$_base_}backend/{$url}/{$action}/{$pkId}/1">
    <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="Username"{if="$action == 'edit'"} value="{$row.username}"{/if}>
    </div>
    <div class="form-group">
        <label for="password">New Password:</label>
        <input type="text" class="form-control" id="password" name="password" placeholder="New Password" value="hello">
    </div>
    <div class="form-group">
        <label for="full_name">Full name:</label>
        <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Full name"{if="$action == 'edit'"} value="{$row.full_name}"{/if}>
    </div>
    <div class="form-group">
        <label for="email">Email:</label>
        <input type="text" class="form-control" id="email" name="email" placeholder="eMail"{if="$action == 'edit'"} value="{$row.email}"{/if}>
    </div>
    {if="$auth_level == $auth_level_administrator"}
    <div class="form-group">
        <label for="auth_level">Authentication Level:</label>
        <select class="form-control" id="auth_level" name="auth_level">
            <option value="{$auth_level_participant}" {if="$action != 'edit' || (isset($row) && $row.auth_level == $auth_level_participant)"}selected{/if}>Participant</option>
            <option value="{$auth_level_presenter}" {if="isset($row) && $row.auth_level == $auth_level_presenter"}selected{/if}>Author</option>
            <option value="{$auth_level_session_chair}" {if="isset($row) && $row.auth_level == $auth_level_session_chair"}selected{/if}>Session Chair</option>
            <option value="{$auth_level_program_chair}" {if="isset($row) && $row.auth_level == $auth_level_program_chair"}selected{/if}>Program Chair</option>
            <option value="{$auth_level_program_chair}" {if="isset($row) && $row.auth_level == $auth_level_program_chair"}selected{/if}>Program Chair</option>
        </select>
    </div>
    {/if}
    <div class="form-group">
        <label for="ticketId">Ticket:</label>
        <select class="form-control" id="ticketId" name="ticketId">
        {loop="$tickets"}
            <option value="{$value.ticketId}">{$value.name}</option>
        {/loop}
        </select>
    </div>
    <div class="form-group">
        <div class="text-right">
            <button class="btn btn-primary">Save</button>&nbsp;<a href="{$_base_}backend/{$url}" class="btn btn-danger">Cancel</a>
        </div>
    </div>
    <input type="hidden" value="{$token}" name="token"/>
</form>
{/if}