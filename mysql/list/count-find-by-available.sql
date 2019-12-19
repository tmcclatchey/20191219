SELECT
    COUNT(`listId`) AS `count`
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