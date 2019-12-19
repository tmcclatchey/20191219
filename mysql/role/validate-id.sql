SELECT
    COUNT(`roleId`) AS `count`
FROM
    `role`
WHERE
    `roleId`=:roleId