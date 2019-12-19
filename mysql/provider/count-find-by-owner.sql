SELECT
    COUNT(`providerId`) AS `count`
FROM
    `provider`
WHERE
    `ownerId`=:ownerId AND 
    (`listName` LIKE CONCAT('%', :query, '%'))