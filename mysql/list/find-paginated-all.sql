SELECT
    `listId`,
    `listName`,
    `providerId`
FROM
    `list`
WHERE
    (`listName` LIKE CONCAT('%', :query, '%'))
LIMIT
    :startingIndex, :recordLimit