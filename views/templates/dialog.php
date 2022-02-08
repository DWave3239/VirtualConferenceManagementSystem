{loop="$overlays"}
<div class="modal fade" id="{$value.id}" tabindex="-1" role="dialog" aria-labelledby="{$value.title}" aria-hidden="true">
    <div class="modal-dialog modal-{$value.size}" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{$value.id}Title">{$value.title}</h5>
                {if="isset($value.print) && $value.print"}
                <a class="print-link">
                    <button type="button" class="btn btn-link print">
                        <i class="fas fa-print"></i>
                    </button>
                </a>
                {/if}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {if="isset($value.content)"}
                    {$value.content}
                {else}
                    please wait...
                {/if}
            </div>
            <div class="modal-footer">
                {if="isset($value.confirmationDialog)"}
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a class="btn btn-danger btn-ok">Delete</a>
                {else}
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                {/if}
            </div>
        </div>
    </div>
</div>
{/loop}