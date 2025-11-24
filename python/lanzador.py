import sys
import os
import json
import argparse
import shutil
import subprocess
import time
import requests 
import uuid

DIR_INTERCAMBIO = r"C:\FirmaONPE\intercambio"
DEFAULT_SIGNER_EXE = r"C:\FirmaONPE\AppFirmaONPE.exe"

def upload_file(url, file_path, token=None):
    files = {
        'documento_firmado': (os.path.basename(file_path), open(file_path, 'rb'), 'application/pdf')
    }
    
    headers = {}
    if token:
        headers['Authorization'] = f'Bearer {token}'
        
    response = requests.post(url, files=files, headers=headers, verify=True) 
    response.raise_for_status()
    return response.json()

def main():
    parser = argparse.ArgumentParser(description='Puente entre Laravel y Firmador ONPE')
    parser.add_argument('--archivo', required=True, help='Ruta absoluta del PDF origen (Laravel)')
    parser.add_argument('--url', required=True, help='URL del endpoint de Laravel para subir el firmado')
    parser.add_argument('--token', help='Token de autenticación (opcional)')
    args = parser.parse_args()
    
    ruta_origen = args.archivo
    url_upload = args.url
    token = args.token
    
    respuesta = {
        'exito': False,
        'error': '',
        'mensaje': '',
        'datos_servidor': None
    }

    ruta_input = None
    ruta_output = None
    proceso = None

    try:
        #  Valida el  origen
        if not os.path.exists(ruta_origen):
            respuesta['error'] = f'Archivo origen no encontrado: {ruta_origen}'
            print(json.dumps(respuesta))
            return

        #  Prepara el directorio de intercambio
        if not os.path.exists(DIR_INTERCAMBIO):
            try:
                os.makedirs(DIR_INTERCAMBIO, exist_ok=True)
            except Exception as e:
                respuesta['error'] = f'No se pudo crear directorio de intercambio: {e}'
                print(json.dumps(respuesta))
                return

        # Copia el archivo a la carpeta de intercambio
        nombre_archivo = os.path.basename(ruta_origen)
        ruta_input = os.path.join(DIR_INTERCAMBIO, nombre_archivo)
        
        # Limpia el archivo si existe
        if os.path.exists(ruta_input):
            try:
                os.remove(ruta_input)
            except:
                pass

        shutil.copy2(ruta_origen, ruta_input)

        nombre_base, ext = os.path.splitext(nombre_archivo)
        nombre_firmado = f"{nombre_base}[F]{ext}"
        ruta_output = os.path.join(DIR_INTERCAMBIO, nombre_firmado)

        # Limpiar output previo si existe
        if os.path.exists(ruta_output):
            try:
                os.remove(ruta_output)
            except:
                pass

        # Ejecutar Firmador
        signer_exe = os.getenv('FIRMADOR_EXE', DEFAULT_SIGNER_EXE)
        
        if os.path.exists(signer_exe):
            try:
                cmd = [signer_exe, ruta_input]
                work_dir = os.path.dirname(signer_exe)
                proceso = subprocess.Popen(cmd, cwd=work_dir)
            except Exception as e:
                respuesta['error'] = f'Error al iniciar firmador: {e}'
                print(json.dumps(respuesta))
                return
        else:
            respuesta['error'] = f'Ejecutable del firmador no encontrado en: {signer_exe}'
            print(json.dumps(respuesta))
            return
        
        # Monitorear creación del archivo firmado
        timeout = 300 
        inicio = time.time()
        firmado = False
        
        # comparar los pesos
        try:
            size_original = os.path.getsize(ruta_input)
        except:
            size_original = -1

        while (time.time() - inicio) < timeout:
            if os.path.exists(ruta_output):
                try:
                    size_new = os.path.getsize(ruta_output)
                    if size_new > 0 and size_new != size_original:
                        time.sleep(2) 
                        firmado = True
                        break
                except:
                    pass
            
            if proceso and proceso.poll() is not None:
                if os.path.exists(ruta_output):
                    try:
                        size_new = os.path.getsize(ruta_output)
                        if size_new > 0 and size_new != size_original:
                            firmado = True
                    except:
                        pass
                break
            
            time.sleep(1)

        # cierra cuando termino el tiempo o se firmo correctamente
        if proceso and proceso.poll() is None:
            try:
                proceso.terminate()
                try:
                    proceso.wait(timeout=5)
                except subprocess.TimeoutExpired:
                    proceso.kill()
            except:
                pass

        if firmado:
            try:
                res_upload = upload_file(url_upload, ruta_output, token)
                respuesta['exito'] = True
                respuesta['mensaje'] = 'Documento firmado y subido exitosamente.'
                respuesta['datos_servidor'] = res_upload
            except Exception as e:
                respuesta['error'] = f'Error subiendo archivo a Servidor: {e}'
        else:
            respuesta['error'] = 'El proceso finalizó sin detectar el documento firmado ([F]).'

    except Exception as e:
        respuesta['error'] = f'Error inesperado en script: {str(e)}'

    finally:
        # Asegurar limpieza de archivos y procesos
        if proceso and proceso.poll() is None:
            try:
                proceso.terminate()
                proceso.wait(timeout=2)
            except:
                try:
                    proceso.kill()
                except:
                    pass

        # Lista de archivos a limpiar (intercambio + origen)
        archivos_a_borrar = [ruta_input, ruta_output]
        if ruta_origen:
            archivos_a_borrar.append(ruta_origen)

        # Limpia los archivos con reintentos
        for archivo in archivos_a_borrar:
            if archivo and os.path.exists(archivo):
                for _ in range(3): # Intentar 3 veces
                    try:
                        os.remove(archivo)
                        break
                    except:
                        time.sleep(1)

    # Imprime el JSON
    print(json.dumps(respuesta))

if __name__ == '__main__':
    main()
