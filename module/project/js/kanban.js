/**
 * Process kanban data
 * @param {string} key          Kanban key, used as kanban id
 * @param {Object} programGroup Group data
 * @returns {Object} kanban data
 */
function processKanbanData(key, programGroup)
{
    var kanbanId = key;

    /* Generate columns */
    var columns = [];
    $.each(kanbanColumns, function(_, column)
    {
        columns.push($.extend({}, column,
        {
            kanban: kanbanId,
            id:     kanbanId + '-' + column.type,
        }));
    });
    /* Format lanes data */
    var lanes = [];
    $.each(programGroup, function(programId, statusMap)
    {
        var programName = programPairs[programId];
        var items       = {doingExecution: []};

        /* Projects and executions */
        ['wait', 'doing', 'closed'].forEach(function(status)
        {
            var itemsList = [];
            var statusProjects = statusMap[status];
            if(statusProjects)
            {
                $.each(statusProjects, function(_, project)
                {
                    var projectID = project.id;
                    var projectItem = $.extend({}, project, {id: 'project-' + projectID, _id: projectID});
                    itemsList.push(projectItem);

                    if(status === 'doing')
                    {
                        var execution = latestExecutions[projectID];
                        if(execution && execution.id)
                        {
                            projectItem.execution = $.extend({}, execution, {id: 'execution-' + execution.id, _id: execution.id});
                        }
                    }
                });
            }
            items[status + 'Project'] = itemsList;
        });

        lanes.push({id: programId, kanban: kanbanId, name: programName, items: items});
    });

    return {id: kanbanId, columns: columns, lanes: lanes};
}

$(function()
{
    /* Init all kanbans */
    $.each(kanbanGroup, function(key, programGroup)
    {
        var $kanban = $('#kanban-' + key);
        if(!$kanban.length) return;
        $kanban.kanban({data: processKanbanData(key, programGroup), maxColHeight: 'auto'});
    });
});
