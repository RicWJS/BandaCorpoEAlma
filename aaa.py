#!/usr/bin/env python3
import os
import sys
from pathlib import Path

def should_skip_file(file_path):
    """Arquivos e pastas que devemos ignorar"""
    skip_extensions = {'.pyc', '.pyo', '.pyd', '.exe', '.dll', '.so', '.dylib', 
                      '.jpg', '.jpeg', '.png', '.gif', '.bmp', '.ico', '.pdf',
                      '.zip', '.tar', '.gz', '.rar', '.7z', '.bin', '.dat'}
    
    skip_folders = {'.git', '__pycache__', 'node_modules', '.vscode', '.idea', 
                   'venv', 'env', '.env', 'dist', 'build', '.pytest_cache'}
    
    # Verifica se está em uma pasta que deve ser ignorada
    for part in file_path.parts:
        if part in skip_folders:
            return True
    
    # Verifica extensão
    if file_path.suffix.lower() in skip_extensions:
        return True
        
    return False

def add_blank_line_to_file(file_path):
    """Adiciona uma linha em branco no final do arquivo se não terminar com \n"""
    try:
        with open(file_path, 'rb') as f:
            content = f.read()
        
        # Se arquivo está vazio, não faz nada
        if not content:
            return False
            
        # Se já termina com \n, não precisa adicionar
        if content.endswith(b'\n'):
            return False
            
        # Adiciona a linha em branco
        with open(file_path, 'ab') as f:
            f.write(b'\n')
        
        return True
        
    except (PermissionError, UnicodeDecodeError, OSError) as e:
        print(f"Erro ao processar {file_path}: {e}")
        return False

def main():
    current_dir = Path('.')
    modified_files = []
    
    print("🔧 Iniciando gambiarra: adicionando linhas em branco...")
    print("=" * 50)
    
    for file_path in current_dir.rglob('*'):
        if file_path.is_file() and not should_skip_file(file_path):
            if add_blank_line_to_file(file_path):
                modified_files.append(str(file_path))
                print(f"✅ Modificado: {file_path}")
    
    print("=" * 50)
    print(f"🎉 Gambiarra concluída!")
    print(f"📊 Total de arquivos modificados: {len(modified_files)}")
    
    if modified_files:
        print("\n📝 Arquivos que foram modificados:")
        for file in modified_files:
            print(f"   - {file}")
        
        print("\n💡 Agora você pode fazer:")
        print("   git add .")
        print('   git commit -m "Formatação: adicionar quebras de linha"')
        print("   git push")
        
        print("\n⚠️  Lembre-se de executar 'remove_blank_lines.py' depois!")
    else:
        print("\n📋 Nenhum arquivo precisou ser modificado.")

if __name__ == "__main__":
    main()
