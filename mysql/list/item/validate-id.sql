SELECT
    COUNT(`itemId`) AS `count`
FROM
    `list_item`
WHERE
    `itemId`=:itemId