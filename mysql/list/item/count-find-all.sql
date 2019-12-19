SELECT
    COUNT(`itemId`) AS `count`
FROM
    `list_item`
WHERE
    (
        (`itemName` LIKE CONCAT('%', :query, '%')) OR
        (`itemValue` LIKE CONCAT('%', :query, '%'))
    )