SELECT
    COUNT(`listId`) AS `count`
FROM
    `list`
WHERE
    `providerId`=:providerId AND 
    (`listName` LIKE CONCAT('%', :query, '%'))