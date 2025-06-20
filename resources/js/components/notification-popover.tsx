import { Button } from "@/components/ui/button"
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover"
import { Separator } from "@/components/ui/separator"
import { useTranslation } from "@/hooks/use-i18n"
import { useNotifications } from "@/hooks/use-notifications"
import { cn } from "@/lib/utils"
import type { Notification } from "@/types"
import { Bell, Check, Trash2, X } from "lucide-react"
import { useState } from "react"

interface NotificationItemProps {
  notification: Notification
  onMarkAsRead: (id: string) => void
  onDelete: (id: string) => void
}

function NotificationItem({ notification, onMarkAsRead, onDelete }: NotificationItemProps) {
  const t = useTranslation()
  const isUnread = !notification.read_at

  const getNotificationTitle = (type: string) => {
    switch (type) {
      case "App\\Notifications\\InvitationSentNotification":
        return t("ui.notifications.invitation_sent")
      case "App\\Notifications\\EmailVerificationOtpNotification":
        return t("ui.notifications.email_verification")
      case "App\\Notifications\\TestNotification":
        return t("ui.notifications.test")
      default:
        return t("ui.notifications.general")
    }
  }

  const getNotificationMessage = (type: string, data: Record<string, unknown>) => {
    switch (type) {
      case "App\\Notifications\\InvitationSentNotification":
        return typeof data.organization === "string"
          ? t("ui.notifications.invitation_sent_message", { organization: data.organization })
          : t("ui.notifications.invitation_sent_general")
      case "App\\Notifications\\EmailVerificationOtpNotification":
        return t("ui.notifications.email_verification_message")
      case "App\\Notifications\\TestNotification":
        return typeof data.message === "string" ? data.message : t("ui.notifications.test_message")
      default:
        return t("ui.notifications.general_message")
    }
  }

  const formatDate = (dateString: string) => {
    const date = new Date(dateString)
    const now = new Date()
    const diffInMs = now.getTime() - date.getTime()
    const diffInMinutes = Math.floor(diffInMs / (1000 * 60))
    const diffInHours = Math.floor(diffInMinutes / 60)
    const diffInDays = Math.floor(diffInHours / 24)

    if (diffInMinutes < 1) return t("ui.time.just_now")
    if (diffInMinutes < 60) return t("ui.time.minutes_ago", { count: diffInMinutes })
    if (diffInHours < 24) return t("ui.time.hours_ago", { count: diffInHours })
    if (diffInDays < 7) return t("ui.time.days_ago", { count: diffInDays })

    return date.toLocaleDateString()
  }

  return (
    <div className={cn("hover:bg-accent/50 flex items-start gap-3 p-3 transition-colors", isUnread && "bg-accent/30")}>
      <div className={cn("mt-2 h-2 w-2 flex-shrink-0 rounded-full", isUnread ? "bg-primary" : "bg-transparent")} />

      <div className="min-w-0 flex-1">
        <div className="flex items-start justify-between gap-2">
          <div className="min-w-0 flex-1">
            <p className="truncate text-sm font-medium">{getNotificationTitle(notification.type)}</p>
            <p className="text-muted-foreground mt-1 text-sm">{getNotificationMessage(notification.type, notification.data)}</p>
            <p className="text-muted-foreground mt-1 text-xs">{formatDate(notification.created_at)}</p>
          </div>

          <div className="flex items-center gap-1">
            {isUnread && (
              <Button
                variant="ghost"
                size="icon"
                className="h-6 w-6"
                onClick={() => onMarkAsRead(notification.id)}
                title={t("ui.notifications.mark_as_read")}
              >
                <Check className="h-3 w-3" />
              </Button>
            )}
            <Button
              variant="ghost"
              size="icon"
              className="text-destructive hover:text-destructive h-6 w-6"
              onClick={() => onDelete(notification.id)}
              title={t("ui.notifications.delete")}
            >
              <Trash2 className="h-3 w-3" />
            </Button>
          </div>
        </div>
      </div>
    </div>
  )
}

export function NotificationPopover() {
  const t = useTranslation()
  const [open, setOpen] = useState(false)
  const { notifications, unreadCount, loading, markAsRead, markAllAsRead, deleteNotification } = useNotifications()

  return (
    <Popover open={open} onOpenChange={setOpen}>
      <PopoverTrigger asChild>
        <Button variant="ghost" size="icon" className="group relative h-9 w-9 cursor-pointer">
          <Bell className="!size-5 opacity-80 group-hover:opacity-100" />
          {unreadCount > 0 && (
            <span className="bg-destructive text-destructive-foreground absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full text-xs font-medium">
              {unreadCount > 9 ? "9+" : unreadCount}
            </span>
          )}
        </Button>
      </PopoverTrigger>

      <PopoverContent className="w-80 p-0" align="end">
        <div className="flex items-center justify-between border-b p-4">
          <h3 className="font-semibold">{t("ui.notifications.title")}</h3>
          <div className="flex items-center gap-2">
            {unreadCount > 0 && (
              <Button variant="ghost" size="sm" onClick={markAllAsRead} className="text-xs">
                {t("ui.notifications.mark_all_read")}
              </Button>
            )}
            <Button variant="ghost" size="icon" className="h-6 w-6" onClick={() => setOpen(false)}>
              <X className="h-4 w-4" />
            </Button>
          </div>
        </div>

        <div className="max-h-96 overflow-y-auto">
          {loading ? (
            <div className="flex items-center justify-center p-8">
              <div className="text-muted-foreground text-sm">{t("ui.messages.loading")}</div>
            </div>
          ) : notifications.length === 0 ? (
            <div className="flex flex-col items-center justify-center p-8">
              <Bell className="text-muted-foreground mb-2 h-8 w-8" />
              <p className="text-muted-foreground text-center text-sm">{t("ui.notifications.empty")}</p>
            </div>
          ) : (
            <div className="divide-y">
              {notifications.map((notification) => (
                <NotificationItem key={notification.id} notification={notification} onMarkAsRead={markAsRead} onDelete={deleteNotification} />
              ))}
            </div>
          )}
        </div>

        {notifications.length > 0 && (
          <>
            <Separator />
            <div className="p-2">
              <Button variant="ghost" className="w-full text-sm" size="sm">
                {t("ui.notifications.view_all")}
              </Button>
            </div>
          </>
        )}
      </PopoverContent>
    </Popover>
  )
}
