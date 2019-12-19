SELECT
    `listId`,
    `listName`,
    `providerId`
FROM
    `list`
WHERE
    `providerId`=0 OR
    `providerId`=:providerId
LIMIT
    :startingIndex, :recordLimit