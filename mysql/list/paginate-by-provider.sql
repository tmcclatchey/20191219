SELECT
    `listId`,
    `listName`,
    `providerId`
FROM
    `list`
WHERE
    `providerId`=:providerId
LIMIT
    :startingIndex, :recordLimit