SELECT
    `listId`,
    `listName`,
    `providerId`
FROM
    `list`
LIMIT
    :startingIndex, :recordLimit