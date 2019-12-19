SELECT
    `listId`,
    `listName`,
    `providerId`
FROM
    `list`
WHERE
    `providerId`=:providerId AND 
    (`listName` LIKE CONCAT('%', :query, '%'))
LIMIT
    :startingIndex, :recordLimit