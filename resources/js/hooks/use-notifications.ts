import { router, usePage } from "@inertiajs/react"
import { useCallback, useEffect, useState } from "react"

import type { Notification, NotificationData } from "@/types"

export function useNotifications() {
  const { props } = usePage<{
    unreadNotificationsCount?: number
  }>()

  const [notifications, setNotifications] = useState<Notification[]>([])
  const [unreadCount, setUnreadCount] = useState(props.unreadNotificationsCount || 0)
  const [loading, setLoading] = useState(false)

  const fetchNotifications = useCallback(async () => {
    try {
      setLoading(true)
      const response = await fetch("/api/notifications", {
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
        },
      })

      if (response.ok) {
        const data: NotificationData = await response.json()
        setNotifications(data.notifications)
        setUnreadCount(data.unread_count)
      }
    } catch (error) {
      console.error("Failed to fetch notifications:", error)
    } finally {
      setLoading(false)
    }
  }, [])

  const markAsRead = useCallback(async (notificationId: string) => {
    try {
      const response = await fetch(`/api/notifications/${notificationId}/read`, {
        method: "PATCH",
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
        },
      })

      if (response.ok) {
        setNotifications((prev) =>
          prev.map((notification) => (notification.id === notificationId ? { ...notification, read_at: new Date().toISOString() } : notification)),
        )
        setUnreadCount((prev) => Math.max(0, prev - 1))
      }
    } catch (error) {
      console.error("Failed to mark notification as read:", error)
    }
  }, [])

  const markAllAsRead = useCallback(async () => {
    try {
      const response = await fetch("/api/notifications/read-all", {
        method: "PATCH",
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
        },
      })

      if (response.ok) {
        setNotifications((prev) =>
          prev.map((notification) => ({
            ...notification,
            read_at: notification.read_at || new Date().toISOString(),
          })),
        )
        setUnreadCount(0)
      }
    } catch (error) {
      console.error("Failed to mark all notifications as read:", error)
    }
  }, [])

  const deleteNotification = useCallback(
    async (notificationId: string) => {
      try {
        const response = await fetch(`/api/notifications/${notificationId}`, {
          method: "DELETE",
          headers: {
            Accept: "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "",
          },
        })

        if (response.ok) {
          const wasUnread = notifications.find((n) => n.id === notificationId)?.read_at === null
          setNotifications((prev) => prev.filter((notification) => notification.id !== notificationId))
          if (wasUnread) {
            setUnreadCount((prev) => Math.max(0, prev - 1))
          }
        }
      } catch (error) {
        console.error("Failed to delete notification:", error)
      }
    },
    [notifications],
  )

  // Sync unread count with shared data
  useEffect(() => {
    if (props.unreadNotificationsCount !== undefined) {
      setUnreadCount(props.unreadNotificationsCount)
    }
  }, [props.unreadNotificationsCount])

  // Fetch notifications on mount
  useEffect(() => {
    fetchNotifications()
  }, [fetchNotifications])

  // Listen for Inertia navigation events to refetch notifications
  useEffect(() => {
    const handleNavigate = () => {
      fetchNotifications()
    }

    const unsubscribe = router.on("navigate", handleNavigate)

    return () => {
      unsubscribe()
    }
  }, [fetchNotifications])

  return {
    notifications,
    unreadCount,
    loading,
    fetchNotifications,
    markAsRead,
    markAllAsRead,
    deleteNotification,
  }
}
