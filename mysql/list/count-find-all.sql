SELECT
    COUNT(`listId`) AS `count`
FROM
    `list`
WHERE
    (`listName` LIKE CONCAT('%', :query, '%'))