SELECT
    COUNT(`providerId`) AS `count`
FROM
    `provider`
WHERE
    (`listName` LIKE CONCAT('%', :query, '%'))