SELECT
    COUNT(`roleId`) AS `count`
FROM
    `role`
WHERE
    `providerId`=:providerId