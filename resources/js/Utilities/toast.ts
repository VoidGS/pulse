import { push } from "notivue";

export interface Toast {
    message: string,
    style: 'success' | 'danger' | 'info' | 'warn' | string
}

export function showToast(message: Toast['message'], style: Toast['style'] = 'success') {
	if (message == '') return

	switch (style) {
		case 'success':
			push.success({ title: 'Sucesso', message: message })
			break
		case 'danger':
			push.error({ title: 'Atenção!', message: message })
			break
		case 'info':
			push.info({ title: 'Informação', message: message })
			break
		case 'warn':
			push.warning({ title: 'Alerta', message: message })
			break
		default:
			push.success({ title: 'Notificação', message: message })
			break
	}
}