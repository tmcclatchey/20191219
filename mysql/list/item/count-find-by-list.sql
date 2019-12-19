SELECT
    COUNT(`itemId`) AS `count`
FROM
    `list_item`
WHERE
    `listId`=:listId AND
    (
        (`itemName` LIKE CONCAT('%', :query, '%')) OR
        (`itemValue` LIKE CONCAT('%', :query, '%'))
    )