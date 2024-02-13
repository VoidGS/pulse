import "momentum-trail"

declare module "momentum-trail" {
    export interface RouterGlobal {"url":"http:\/\/pulsehub.local","port":null,"defaults":[],"routes":{"login":{"uri":"login","methods":["GET","HEAD"]},"logout":{"uri":"logout","methods":["POST"]},"password.request":{"uri":"forgot-password","methods":["GET","HEAD"]},"password.reset":{"uri":"reset-password\/{token}","methods":["GET","HEAD"],"parameters":["token"]},"password.email":{"uri":"forgot-password","methods":["POST"]},"password.update":{"uri":"reset-password","methods":["POST"]},"user-profile-information.update":{"uri":"user\/profile-information","methods":["PUT"]},"user-password.update":{"uri":"user\/password","methods":["PUT"]},"password.confirmation":{"uri":"user\/confirmed-password-status","methods":["GET","HEAD"]},"password.confirm":{"uri":"user\/confirm-password","methods":["POST"]},"two-factor.login":{"uri":"two-factor-challenge","methods":["GET","HEAD"]},"two-factor.enable":{"uri":"user\/two-factor-authentication","methods":["POST"]},"two-factor.confirm":{"uri":"user\/confirmed-two-factor-authentication","methods":["POST"]},"two-factor.disable":{"uri":"user\/two-factor-authentication","methods":["DELETE"]},"two-factor.qr-code":{"uri":"user\/two-factor-qr-code","methods":["GET","HEAD"]},"two-factor.secret-key":{"uri":"user\/two-factor-secret-key","methods":["GET","HEAD"]},"two-factor.recovery-codes":{"uri":"user\/two-factor-recovery-codes","methods":["GET","HEAD"]},"profile.show":{"uri":"user\/profile","methods":["GET","HEAD"]},"other-browser-sessions.destroy":{"uri":"user\/other-browser-sessions","methods":["DELETE"]},"current-user-photo.destroy":{"uri":"user\/profile-photo","methods":["DELETE"]},"teams.create":{"uri":"teams\/create","methods":["GET","HEAD"]},"teams.store":{"uri":"teams","methods":["POST"]},"teams.show":{"uri":"teams\/{team}","methods":["GET","HEAD"],"parameters":["team"]},"teams.update":{"uri":"teams\/{team}","methods":["PUT"],"parameters":["team"]},"teams.destroy":{"uri":"teams\/{team}","methods":["DELETE"],"parameters":["team"]},"current-team.update":{"uri":"current-team","methods":["PUT"]},"team-members.store":{"uri":"teams\/{team}\/members","methods":["POST"],"parameters":["team"]},"team-members.update":{"uri":"teams\/{team}\/members\/{user}","methods":["PUT"],"parameters":["team","user"]},"team-members.destroy":{"uri":"teams\/{team}\/members\/{user}","methods":["DELETE"],"parameters":["team","user"]},"team-invitations.accept":{"uri":"team-invitations\/{invitation}","methods":["GET","HEAD"],"parameters":["invitation"]},"team-invitations.destroy":{"uri":"team-invitations\/{invitation}","methods":["DELETE"],"parameters":["invitation"]},"sanctum.csrf-cookie":{"uri":"sanctum\/csrf-cookie","methods":["GET","HEAD"]},"dashboard":{"uri":"dashboard","methods":["GET","HEAD"]},"users.index":{"uri":"users","methods":["GET","HEAD"]},"users.create":{"uri":"users\/create","methods":["GET","HEAD"]},"users.store":{"uri":"users","methods":["POST"]},"users.update":{"uri":"users\/{user}","methods":["PUT","PATCH"],"parameters":["user"]}},"wildcards":{"password.*":[],"user-profile-information.*":[],"user-password.*":[],"two-factor.*":[],"profile.*":[],"other-browser-sessions.*":[],"current-user-photo.*":[],"teams.*":[],"current-team.*":[],"team-members.*":[],"team-invitations.*":[],"sanctum.*":[],"users.*":[]}}
}