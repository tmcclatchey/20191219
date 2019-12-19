SELECT
    COUNT(`roleId`) AS `count`
FROM
    `role`
WHERE
    (
        `providerId`=:providerId
    )
    AND
    (
        `roleName` LIKE CONCAT('%', :query, '%') OR
        `roleDescription` LIKE CONCAT('%', :query, '%')
    )