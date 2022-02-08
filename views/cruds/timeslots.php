{if="$action == 'list'"}
<table class="table table-sm">
    <thead>
        <tr>
            <th>Date</th>
            <th>From</th>
            <th>Until</th>
            <th>
                <a href="{$_base_}backend/{$url}/add" class="btn btn-primary">Add</a>
            </th>
        </tr>
    </thead>
    <tbody>
    {loop="$list"}
        <tr>
            <td>{$value.date}</td>
            <td>{$value.time_from}</td>
            <td>{$value.time_until}</td>
            <td>
                <a href="{$_base_}backend/{$url}/edit/{$value.timeslotId}" class="btn btn-primary">Edit</a>
                <a href="#" data-href="{$_base_}backend/{$url}/delete/{$value.timeslotId}" class="btn btn-primary" data-toggle="modal" data-target="#crudDeletionOverlay">Delete</a>
            </td>
        </tr>
    {/loop}
    </tbody>
</table>
{/if}

{if="$action == 'edit' || $action == 'add'"}
<form method="post" action="{$_base_}backend/{$url}/{$action}/{$pkId}/1">
    <div class="form-group">
        <label for="date">Date:</label>
        <input type="date" class="form-control" id="date" name="date" {if="$action == 'edit'"} value="{$row.date}"{/if}>
    </div>
    <div class="form-group">
        <label for="time_from">Time from:</label>
        <input type="time" class="form-control" id="time_from" name="time_from" {if="$action == 'edit'"} value="{function="substr($row.time_from, 0, -3)"}"{/if}>
    </div>
    <div class="form-group">
        <label for="time_until">Time until:</label>
        <input type="time" class="form-control" id="time_until" name="time_until" {if="$action == 'edit'"} value="{function="substr($row.time_until, 0, -3)"}"{/if}>
    </div>
    <div class="form-group">
        <div class="text-right">
            <button class="btn btn-primary">Save</button>&nbsp;<a href="{$_base_}backend/{$url}" class="btn btn-danger">Cancel</a>
        </div>
    </div>
    <input type="hidden" value="{$token}" name="token"/>
</form>
{/if}