SELECT
    `listId`,
    `listName`,
    `providerId`
FROM
    `list`
WHERE
    (
        `providerId`=0 OR
        `providerId`=:providerId
    ) 
    AND 
    (
        `listName` LIKE CONCAT('%', :query, '%')
    )
LIMIT
    :startingIndex, :recordLimit